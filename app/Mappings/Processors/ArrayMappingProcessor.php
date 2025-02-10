<?php

namespace App\Mappings\Processors;

use Illuminate\Support\Arr;
use App\Models\Mapping;

class ArrayMappingProcessor extends AbstractMappingProcessor
{
    private array $data;

    public function __construct(array $data, Mapping $mapping)
    {
        parent::__construct($mapping);
        $this->data = $data;
    }

    protected function getInitialValue(): mixed
    {
        return Arr::get($this->data, $this->mapping->path, $this->mapping->default);
    }
}
