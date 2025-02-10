<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Http;

class GetInventory
{
    use AsAction;

    protected function getNextAvailableProperty(array $validated)
    {
        $route = __(config('homeful-contracts.end-points.inventory'), $validated);
        $response = Http::acceptJson()->get($route);

        return $response->ok() ? $response->json('data') : false;
    }

    public function handle(array $attribs): mixed
    {
        $validated = validator($attribs, $this->rules())->validate();

        return $this->getNextAvailableProperty($validated);
    }

    public function rules(): array
    {
        return [
            'sku' => ['required', 'string', 'min:4']
        ];
    }
}
