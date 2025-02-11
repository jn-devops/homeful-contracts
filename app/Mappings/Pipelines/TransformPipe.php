<?php

namespace App\Mappings\Pipelines;

use App\Enums\MappingTransformers;
use App\Models\Mapping;
use Closure;
use Illuminate\Support\Facades\App;

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
}
