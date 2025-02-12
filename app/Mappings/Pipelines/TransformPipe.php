<?php

namespace App\Mappings\Pipelines;

use App\Exceptions\MissingJoinTransformerException;
use Illuminate\Support\Facades\App;
use App\Enums\MappingTransformers;
use App\Models\Mapping;
use Closure;

/**
 * Class TransformPipe
 *
 * This pipeline step applies a series of transformations to the input value using Fractal transformers.
 * It dynamically resolves transformers using the `MappingTransformers` enum.
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
     * @throws MissingJoinTransformerException
     */
    public function handle(mixed $value, Closure $next): mixed
    {
        // If no transformers are specified, skip to the next pipeline step
        if (empty($this->mapping->transformer)) {
            return $next($value);
        }

        $transformers = is_string($this->mapping->transformer)
            ? array_map('trim', explode(',', $this->mapping->transformer))
            : (array) $this->mapping->transformer;

        // 🚨 Validate: If multiple paths are used, ensure JoinTransformer is included

        if ($this->hasMultiplePaths() && !$this->containsJoinTransformer($transformers)) {
            throw new MissingJoinTransformerException($this->mapping->code);
        }

        foreach ($transformers as $transformerWithOption) {
            [$transformerName, $option] = $this->parseTransformerWithOption($transformerWithOption);

            // Use MappingTransformers to resolve the transformer class
            $enumTransformer = MappingTransformers::find($transformerName);

            if ($enumTransformer) {
                $transformerClass = $enumTransformer->transformer();

                $transformerInstance = $option
                    ? App::make($transformerClass, ['option' => $option])
                    : App::make($transformerClass);

                $value = fractal()
                    ->item(['value' => $value])
                    ->transformWith($transformerInstance)
                    ->toArray()['data']['value'];

            }
        }

        // Pass the transformed value to the next step in the pipeline
        return $next($value);
    }

    /**
     * Check if the mapping's path contains multiple values (comma-separated paths).
     *
     * @return bool
     */
    protected function hasMultiplePaths(): bool
    {
        return str_contains($this->mapping->path, ',');
    }

    /**
     * Parse transformer and its option (if provided) in the format `TransformerName?option=value`.
     *
     * @param string $transformerWithOption
     * @return array
     */
    protected function parseTransformerWithOption(string $transformerWithOption): array
    {
        if (str_contains($transformerWithOption, '?')) {
            [$transformer, $option] = explode('?', $transformerWithOption, 2);
            return [$transformer, $option];  // Return both transformer and its options
        }

        return [$transformerWithOption, null];  // No options specified
    }

    /**
     * Check if the transformer list includes `JoinTransformer` (case-insensitive).
     *
     * @param array $transformers List of transformer names.
     * @return bool True if `JoinTransformer` is found, false otherwise.
     */
    protected function containsJoinTransformer(array $transformers): bool
    {
        foreach ($transformers as $transformerName) {
            if (MappingTransformers::find($transformerName) === MappingTransformers::JOIN) {
                return true;
            }
        }
        return false;
    }
}
