<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};
use App\Models\{Product, Project, Property};
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class, WithFaker::class);

test('fetch products from server', function () {
    $sku = 'JN-PPMP-HLTH-EUWF';
    $route = __(config('homeful-contracts.end-points.product'), ['sku' => $sku]);
    $response = Http::acceptJson()->get($route);
    expect($response->status())->toBe(200);
    expect($response->json('data.sku'))->toBe($sku);
})->skip();

test('external product model works', function () {
    $sku = 'JN-PPMP-HLTH-EUWF';
    $product = Product::where('sku', $sku)->first();
    if ($product instanceof Product) {
        expect($product->sku)->toBe($sku);
    }
});

test('external project model works', function () {
    $code = 'PVMP';
    $project = Project::where('code', $code)->first();
    if ($project instanceof Project) {
        expect($project->code)->toBe($code);
    }
});

test('external property model works', function () {
    $code = 'PVMP-01-002-001';
    $property = Property::where('code', $code)->first();
    if ($property instanceof Property) {
        $property->load('product', 'project');
        dd($property);
        expect($property->code)->toBe($code);
        expect($property->product->sku)->toBe($property->sku);
        expect($property->project->code)->toBe($property->project_code);
    }
});

//test('external project has factory', function () {
//   $project = Project::factory()->create();
//});
