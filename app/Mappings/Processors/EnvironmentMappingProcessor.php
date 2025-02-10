<?php

namespace App\Mappings\Processors;

class EnvironmentMappingProcessor extends AbstractMappingProcessor
{
    protected function getInitialValue(): mixed
    {
        return env($this->mapping->path, $this->mapping->default);
    }
}
