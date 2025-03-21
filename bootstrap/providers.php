<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\HorizonServiceProvider::class,
    Homeful\Contracts\Providers\EventServiceProvider::class,
    Homeful\Mortgage\Providers\EventServiceProvider::class,
    Spatie\SchemalessAttributes\SchemalessAttributesServiceProvider::class,
];
