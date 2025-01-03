<?php

use Homeful\Contracts\Providers\EventServiceProvider as ContractEventServiceProvider;
use Homeful\Mortgage\Providers\EventServiceProvider as MortgageServiceProvider;
use Spatie\SchemalessAttributes\SchemalessAttributesServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    SchemalessAttributesServiceProvider::class,
    MortgageServiceProvider::class,
    ContractEventServiceProvider::class,
];
