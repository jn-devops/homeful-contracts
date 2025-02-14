<?php

namespace App\Actions;

use App\Mappings\Processors\SearchParamsMappingProcessor;
use App\Mappings\Processors\EnvironmentMappingProcessor;
use App\Mappings\Processors\MFilesMappingProcessor;
use App\Mappings\Processors\ConfigMappingProcessor;
use App\Mappings\Processors\ArrayMappingProcessor;
use Lorisleiva\Actions\Concerns\AsAction;
use Homeful\Contracts\Models\Contract;
use App\Enums\MappingSource;
use App\Models\Mapping;


class GetContractPayload
{
    use AsAction;

    public function handle(Contract $contract, Mapping $mapping): mixed
    {
        $data = $contract->getData()->toArray();
        $property_code = $contract->inventory->code;

        return match ($mapping->source) {
            MappingSource::ARRAY => (new ArrayMappingProcessor($mapping, $data))->process(),
            MappingSource::CONFIG => (new ConfigMappingProcessor($mapping))->process(),
            MappingSource::ENVIRONMENT => (new EnvironmentMappingProcessor($mapping))->process(),
            MappingSource::SEARCH_PARAMS => (new SearchParamsMappingProcessor($mapping))->process(),
            MappingSource::MFILES => (new MFilesMappingProcessor($mapping, $property_code))->process(),
            default => null,
        };
    }
}
