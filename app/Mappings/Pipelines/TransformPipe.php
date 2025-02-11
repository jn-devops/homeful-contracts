<?php

namespace App\Mappings\Pipelines;

use App\Models\Mapping;
use Closure;

/**
 * Class TransformPipe
 *
 * This pipeline step applies a series of transformations to the input value using Fractal transformers.
 * The transformers can be dynamically resolved from fully qualified class names or subdirectories under
 * the default namespace `App\Transformers`.
 */
class TransformPipe
{
    protected Mapping $mapping;

    /**
     * Initialize the pipe with the given mapping configuration.
     *
     * @param Mapping $mapping The mapping that defines transformers and other metadata.
     */
    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Apply a series of transformations to the value and pass it to the next pipeline step.
     *
     * This method resolves and applies one or more transformers specified in the mapping.
     * It supports both individual and comma-separated lists of transformers.
     *
     * @param mixed $value The value to be transformed.
     * @param Closure $next The next step in the pipeline.
     * @return mixed The transformed value.
     */
    public function handle(mixed $value, Closure $next): mixed
    {
        // If no transformers are specified, skip to the next pipeline step
        if (empty($this->mapping->transformer)) {
            return $next($value);
        }

        // Support both string (comma-separated) and array transformer formats
        $transformerClasses = is_string($this->mapping->transformer)
            ? array_map('trim', explode(',', $this->mapping->transformer))
            : (array) $this->mapping->transformer;

        // Apply each transformer in sequence
        foreach ($transformerClasses as $transformerName) {
            $transformerClass = $this->findTransformerClass($transformerName);

            if ($transformerClass) {
                $value = fractal()
                    ->item(['value' => $value])
                    ->transformWith(new $transformerClass())
                    ->toArray()['data']['value'];
            }
        }

        // Pass the transformed value to the next step in the pipeline
        return $next($value);
    }

    /**
     * Dynamically find and return the fully qualified class name of a transformer.
     *
     * This method searches through the `App\Mappings\Transformers` namespace and its subdirectories
     * to find a class that matches the given transformer name.
     *
     * @param string $transformerName The short or full name of the transformer (with or without "Transformer").
     * @return string|null The fully qualified class name if found, otherwise null.
     */
    protected function findTransformerClass(string $transformerName): ?string
    {
        // Retrieve base namespace from config
        $baseNamespace = config('homeful-contracts.transformers.base_namespace');

        // Derive base directory from base namespace
        $baseDirectory = base_path(str_replace('\\', DIRECTORY_SEPARATOR, $baseNamespace));

        // Ensure the transformer name ends with "Transformer" (if not specified)
        if (!str_ends_with($transformerName, 'Transformer')) {
            $transformerName .= 'Transformer';
        }

        // Use Recursive Directory Iterator to scan subdirectories
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($baseDirectory));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $className = $this->buildClassName($file, $baseNamespace, $baseDirectory);
                if (class_exists($className) && str_ends_with($className, $transformerName)) {
                    return $className;
                }
            }
        }

        return null;  // Return null if no matching class is found
    }

    /**
     * Build the fully qualified class name of a transformer.
     *
     * Converts the file path to a namespace and appends the class name.
     * This allows support for nested directories under `App\Transformers`.
     *
     * @param \SplFileInfo $file The file representing the transformer class.
     * @param string $baseNamespace The base namespace for transformers.
     * @param string $baseDirectory The base directory for transformers.
     * @return string The fully qualified class name of the transformer.
     */
    protected function buildClassName(\SplFileInfo $file, string $baseNamespace, string $baseDirectory): string
    {
        // Get the relative path of the file and convert it to a namespace
        $relativePath = str_replace($baseDirectory, '', $file->getPath());
        $relativeNamespace = trim(str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath), '\\');

        // Build the fully qualified class name
        $className = $baseNamespace;
        if (!empty($relativeNamespace)) {
            $className .= "\\{$relativeNamespace}";
        }

        // Append the class name (file name without the ".php" extension)
        return "{$className}\\{$file->getBasename('.php')}";
    }
}
