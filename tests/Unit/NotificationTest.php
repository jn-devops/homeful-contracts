<?php

use Illuminate\Foundation\Testing\{RefreshDatabase, WithFaker};

use App\Actions\Contract\Avail;
use App\Actions\Contract\Consult;
use Illuminate\Support\Facades\Notification;
use Homeful\Notifications\Notifications\OnboardedToPaidBuyerNotification;
use Homeful\References\Data\ReferenceData;
use Homeful\References\Models\Reference;
use Illuminate\Support\Facades\Route;
use Homeful\References\Facades\References;
use LBHurtado\EngageSpark\Notifications\Adhoc;
use Homeful\Contracts\Models\Contract;

//uses(RefreshDatabase::class, WithFaker::class);

dataset('reference', function() {
    return [
        [
            fn () => with(app(Consult::class)->run(getHomefulId()), function (Reference $reference) {
                return app(Avail::class)->run($reference, ['sku' => getProductSKU()]);
            })
        ]
    ];
});

test('it can send', function (Reference $reference) {


    $reference = Reference::where('code','JN-X77B67')->first();
    $referenceData = ReferenceData::fromModel($reference);
//    $contract = $reference->getContract();
//    dd()
//    $contract->notify(new OnboardedToPaidBuyerNotification($referenceData));
//
//
////    $notification = new OnboardedToPaidBuyerNotification($referenceData);
//    Notification::route('mail', 'lester@hurtado.ph')->route('engagespark', '09173011987')->notify(new OnboardedToPaidBuyerNotification($referenceData));
//
//    $reference->get



})->with('reference');
