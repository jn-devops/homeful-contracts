<?php

namespace App\Actions;

use Homeful\Properties\Models\Property as Inventory;
use Lorisleiva\Actions\Concerns\AsAction;

class GetNextInventory
{
    use AsAction;

    public function handle(string $sku): ?Inventory
    {
        return Inventory::where('sku', $sku)->first();
    }
}
