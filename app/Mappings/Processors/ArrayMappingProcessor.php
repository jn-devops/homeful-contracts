<?php

namespace App\Mappings\Processors;

use Illuminate\Support\Arr;
use App\Models\Mapping;

class ArrayMappingProcessor extends AbstractMappingProcessor
{
    private array $data;

    public function __construct(Mapping $mapping, array $data)
    {
        parent::__construct($mapping);
        $this->data = $data;
    }

    protected function getInitialValue(): mixed
    {
        $paths = explode(',', $this->mapping->path);

        // If only one path exists, return the value as before (backward compatibility)
        if (count($paths) === 1) {
            return Arr::get($this->data, trim($paths[0]), $this->mapping->default);
        }

        // Retrieve multiple values and return as JSON
        $values = [];

        foreach ($paths as $path) {
            $key = trim($path);
            $value = Arr::get($this->data, $key, null);

            if (!is_null($value)) {
                $values[$key] = $value; // Maintain key-value structure
            }
        }

        return json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
