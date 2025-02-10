<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;
use Illuminate\Pipeline\Pipeline;
use App\Enums\MappingSource;
use Illuminate\Support\Arr;
use App\Models\Mapping;


class GetContractPayload
{
    use AsAction;

    public function handle(Contract $contract, Mapping $mapping)
    {
        $data = $contract->getData();
        $array = $data->toArray();
        return match ($mapping->source) {
            MappingSource::ARRAY => $this->processArrayMapping($array, $mapping),
            MappingSource::CONFIG => $this->processConfigMapping($mapping),
            default => null,
        };
    }

    protected function processConfigMapping(Mapping $mapping)
    {
        return app(Pipeline::class)
            ->send(config($mapping->path, $mapping->default))
            ->through([
                fn($value) => $this->resolveAndApplyTransformer($value, $mapping->transformer),
                fn($value) => $mapping->type->castValue($value)
            ])
            ->thenReturn();
    }

    protected function processArrayMapping(array $array, Mapping $mapping): mixed
    {
        return app(Pipeline::class)
            ->send(Arr::get($array, $mapping->path, $mapping->default))
            ->through([
                fn($value) => $this->resolveAndApplyTransformer($value, $mapping->transformer),
                fn($value) => $mapping->type->castValue($value)
            ])
            ->thenReturn();
    }

    protected function resolveAndApplyTransformer(mixed $value, string|array|null $transformer): mixed
    {
        // If no transformer is specified, return the original value
        if (empty($transformer)) {
            return $value;
        }

        // Convert a comma-delimited string into an array
        if (is_string($transformer)) {
            $transformer = array_map('trim', explode(',', $transformer));
        }

        // Iterate through each transformer in the array
        foreach ($transformer as $transformerClass) {
            // Dynamically resolve the transformer using the default namespace if needed
            $transformerClass = class_exists($transformerClass)
                ? $transformerClass  // Fully qualified class
                : "App\\Transformers\\{$transformerClass}";  // Default namespace

            // If the transformer class exists, resolve and apply transformation
            if (class_exists($transformerClass)) {
                $transformerInstance = app($transformerClass);

                if ($transformerInstance instanceof \App\Transformers\TransformerInterface) {
                    $value = $transformerInstance->transform($value);  // Apply transformation
                }
            }
        }

        return $value;
    }
}
