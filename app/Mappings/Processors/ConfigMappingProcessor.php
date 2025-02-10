<?php

namespace App\Mappings\Processors;

namespace App\Mappings\Processors;

class ConfigMappingProcessor extends AbstractMappingProcessor
{
    protected function getInitialValue(): mixed
    {
        return config($this->mapping->path, $this->mapping->default);
    }
}
