<?php

namespace App\Mappings\Processors;

class SearchParamsMappingProcessor extends AbstractMappingProcessor
{
    protected function getInitialValue(): mixed
    {
        return request()->query($this->mapping->path, $this->mapping->default);
    }
}
