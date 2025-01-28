<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use Homeful\Properties\Data\{ProjectData, PropertyData};
use Homeful\Properties\Models\{Project, Property};
use Homeful\Products\Data\ProductData;
use Homeful\Products\Models\Product;

uses(RefreshDatabase::class, WithFaker::class);

test('property db configuration works', function () {
    $property = app(Property::class);
    expect($property->getConnectionName())->toBe(config('properties.models.property.connection'));
    expect($property->getTable())->toBe(config('properties.models.property.table'));
    $project = app(Project::class);
    expect($project->getConnectionName())->toBe(config('properties.models.project.connection'));
    expect($project->getTable())->toBe(config('properties.models.project.table'));
    $product = app(Product::class);
    expect($product->getConnectionName())->toBe(config('products.models.product.connection'));
    expect($product->getTable())->toBe(config('products.models.product.table'));

});

dataset('sku', function () {
    return [
        ['JN-PPMP-HLTH-EUWF'],
        ['JN-PPMP-HLTH-IU'],
        ['JN-PPMP-HLTH-IUWF']
    ];
});

test('external product model works', function (string $sku) {
    $product = app(Product::class)->whereSku($sku)->first();
    expect($product->sku)->toBe($sku);
})->with('sku');


dataset('project code', function () {
    return [
        ['PVMP'],
        ['PVSN'],
        ['ESE']
    ];
});

test('external project model works', function (string $code) {
    $project = app(Project::class)->whereCode($code)->first();
    expect($project->code)->toBe($code);
})->with('project code');

dataset('property code', function () {
    return [
        ['PVMP-01-002-001'],
        ['PHMP-00-029-001'],
        ['PPMP-00-025-034']
    ];
});

test('external property model with relations works', function (string $code) {
    $property = app(Property::class)->whereCode($code)->first();
    $property->load('product', 'project');
    expect($property->code)->toBe($code);
    expect($property->product->sku)->toBe($property->sku);
    expect($property->project->code)->toBe($property->project_code);
})->with('property code');

test('external property has factory', function () {
    $property = Property::factory()->forProduct()->forProject()->create();
    expect($property)->toBeInstanceOf(Property::class);
    expect($property->product)->toBeInstanceOf(Product::class);
    expect($property->project)->toBeInstanceOf(Project::class);
    expect($propertyData = PropertyData::fromModel($property))->toBeInstanceOf(PropertyData::class);
    expect($propertyData->product)->toBeInstanceOf(ProductData::class);
    expect($propertyData->project)->toBeInstanceOf(ProjectData::class);
})->skip();
