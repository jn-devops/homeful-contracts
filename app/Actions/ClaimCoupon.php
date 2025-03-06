<?php

namespace App\Actions;

use Lorisleiva\Actions\Concerns\AsAction;

class ClaimCoupon
{
    use AsAction;

    public function handle(string $code): float
    {
        $validated = validator(compact('code'), [
            'code' => ['required', 'string', 'min:4']
        ])->validate();

        return $this->getAmount($validated);
    }

    protected function getAmount(array $validated): float
    {
        return 10000;
    }
}
