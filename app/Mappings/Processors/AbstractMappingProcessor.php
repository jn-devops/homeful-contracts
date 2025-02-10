<?php

namespace App\Mappings\Processors;

use Illuminate\Pipeline\Pipeline;
use App\Models\Mapping;

/**
 * AbstractMappingProcessor
 *
 * This abstract class handles the core logic for processing mappings in a generic way.
 * It uses a pipeline to transform the retrieved value and cast it to the correct type.
 * Subclasses are responsible for implementing how the initial value is retrieved (Step 1).
 *
 * Core Steps:
 * 1. Retrieve the initial value (`getInitialValue`) — to be implemented in subclasses.
 * 2. Apply any specified transformers.
 * 3. Cast the final result using the specified mapping type.
 *
 * Example Usage (in subclasses):
 * ```php
 * $processor = new ArrayMappingProcessor($data, $mapping);
 * $result = $processor->process();
 * ```
 */
abstract class AbstractMappingProcessor
{
    protected Mapping $mapping;

    /**
     * Initialize the processor with the given mapping.
     *
     * @param Mapping $mapping The mapping model containing transformation details.
     */
    public function __construct(Mapping $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * Main processing logic for the mapping.
     *
     * Steps:
     * - Step 1: Get the initial value (subclass-specific logic).
     * - Step 2: Apply the transformers (if any).
     * - Step 3: Cast the final value using the specified type.
     *
     * @return mixed The final processed value.
     */
    public function process(): mixed
    {
        // Step 1: Get the initial value (delegated to subclass)
        $value = $this->getInitialValue();

        // Step 2 and Step 3: Apply transformations and cast the value
        return app(Pipeline::class)
            ->send($value)
            ->through([
                // Apply transformations
                fn($value) => $this->resolveAndApplyTransformer($value, $this->mapping->transformer),

                // Cast to the correct type
                fn($value) => $this->mapping->type->castValue($value),
            ])
            ->thenReturn();
    }

    /**
     * Retrieve the initial value for the mapping.
     *
     * This is the only step delegated to subclasses. Each subclass will
     * implement this based on the data source (e.g., array, config, environment).
     *
     * @return mixed The initial value to be processed.
     */
    abstract protected function getInitialValue(): mixed;

    /**
     * Apply transformations to the value using Fractal transformers.
     *
     * The transformers are resolved dynamically, supporting both fully-qualified class
     * names and those in subdirectories of the default `App\Transformers` namespace.
     *
     * @param mixed $value The value to transform.
     * @param string|array|null $transformer A comma-delimited string or array of transformers.
     * @return mixed The transformed value.
     */
    protected function resolveAndApplyTransformer(mixed $value, string|array|null $transformer): mixed
    {
        if (empty($transformer)) {
            return $value;  // No transformer to apply
        }

        // Convert comma-delimited string to array if needed
        if (is_string($transformer)) {
            $transformer = array_map('trim', explode(',', $transformer));
        }

        // Apply each transformer in sequence
        foreach ($transformer as $transformerName) {
            $transformerClass = $this->findTransformerClass($transformerName);

            if ($transformerClass) {
                $value = fractal()
                    ->item(['value' => $value])  // Wrap primitive in array format
                    ->transformWith(new $transformerClass())  // Apply the transformation
                    ->toArray()['data']['value'];  // Extract transformed value
            }
        }

        return $value;
    }

    /**
     * Dynamically find and return the fully qualified class name of the transformer.
     *
     * This method scans the `App\Transformers` namespace and its subdirectories to find
     * any class that matches the given transformer name. Supports nested subdirectories.
     *
     * @param string $transformerName The short or full name of the transformer.
     * @return string|null The fully qualified class name if found, otherwise null.
     */
    protected function findTransformerClass(string $transformerName): ?string
    {
        $baseNamespace = "App\\Transformers";
        $baseDirectory = app_path('Transformers');

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
     * Converts the file path into a namespace and appends the class name.
     * Handles nested directories under `App\Transformers`.
     *
     * @param \SplFileInfo $file The file representing the transformer class.
     * @param string $baseNamespace The base namespace for transformers.
     * @param string $baseDirectory The base directory for transformers.
     * @return string The fully qualified class name.
     */
    protected function buildClassName(\SplFileInfo $file, string $baseNamespace, string $baseDirectory): string
    {
        // Get the relative path and convert it to a namespace
        $relativePath = str_replace($baseDirectory, '', $file->getPath());
        $relativeNamespace = trim(str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath), '\\');

        $className = $baseNamespace;
        if (!empty($relativeNamespace)) {
            $className .= "\\{$relativeNamespace}";
        }

        // Append the class name (file name without extension)
        return "{$className}\\{$file->getBasename('.php')}";
    }
}
