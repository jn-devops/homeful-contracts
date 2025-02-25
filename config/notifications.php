<?php

use Homeful\Notifications\Notifications\AcknowledgedToPrequalifiedBuyerNotification;
use Homeful\Notifications\Notifications\AddCoBorrowerBuyerNotification;
use Homeful\Notifications\Notifications\ApprovedToCancelledBuyerNotification;
use Homeful\Notifications\Notifications\ApprovedToValidatedBuyerNotification;
use Homeful\Notifications\Notifications\AssignedToAcknowledgedBuyerNotification;
use Homeful\Notifications\Notifications\AssignedToIdledBuyerNotification;
use Homeful\Notifications\Notifications\CoBorrowerNotification;
use Homeful\Notifications\Notifications\DisapprovedToOverriddenBuyerNotification;
use Homeful\Notifications\Notifications\IdledToAcknowledgedBuyerNotification;
use Homeful\Notifications\Notifications\OnboardedToPaidBuyerNotification;
use Homeful\Notifications\Notifications\OnboardedToPaymentFailedBuyerNotification;
use Homeful\Notifications\Notifications\OverriddenToCancelledBuyerNotification;
use Homeful\Notifications\Notifications\OverriddenToValidatedBuyerNotification;
use Homeful\Notifications\Notifications\PaidToAssignedBuyerNotification;
use Homeful\Notifications\Notifications\PaymentFailedToPaidBuyerNotification;
use Homeful\Notifications\Notifications\PrequalifiedToNotQualifiedBuyerNotification;
use Homeful\Notifications\Notifications\PrequalifiedToQualifiedBuyerNotification;
use Homeful\Notifications\Notifications\QualifiedToApprovedBuyerNotification;
use Homeful\Notifications\Notifications\QualifiedToDisapprovedBuyerNotification;
use Homeful\Notifications\Notifications\ReuploadDocumentBuyerNotification;
use Homeful\Notifications\Notifications\TestNotification;
use Homeful\Notifications\Notifications\ValidatedToCancelledBuyerNotification;
use Homeful\Notifications\Notifications\VerifiedToOnboardedBuyerNotification;

return [
    'channels' => [
        'default' => array_filter(explode(',', env('DEFAULT_CHANNELS', 'database'))),
        'allowed' => array_filter(explode(',', env('ALLOWED_CHANNELS', 'database,slack'))),
        AcknowledgedToPrequalifiedBuyerNotification::class => array_filter(explode(',', env('AcknowledgedToPrequalifiedBuyerNotification', 'mail,engage_spark'))),
        AddCoBorrowerBuyerNotification::class => array_filter(explode(',', env('AddCoBorrowerBuyerNotification', 'mail,engage_spark'))),
        ApprovedToCancelledBuyerNotification::class => array_filter(explode(',', env('ApprovedToCancelledBuyerNotification', 'mail,engage_spark'))),
        ApprovedToValidatedBuyerNotification::class => array_filter(explode(',', env('ApprovedToValidatedBuyerNotification', 'mail,engage_spark'))),
        AssignedToAcknowledgedBuyerNotification::class => array_filter(explode(',', env('AssignedToAcknowledgedBuyerNotification', 'mail,engage_spark'))),
        AssignedToIdledBuyerNotification::class => array_filter(explode(',', env('AssignedToIdledBuyerNotification', 'mail,engage_spark'))),
        CoBorrowerNotification::class => array_filter(explode(',', env('CoBorrowerNotification', 'mail,engage_spark'))),
        DisapprovedToOverriddenBuyerNotification::class => array_filter(explode(',', env('DisapprovedToOverriddenBuyerNotification', 'mail,engage_spark'))),
        IdledToAcknowledgedBuyerNotification::class => array_filter(explode(',', env('IdledToAcknowledgedBuyerNotification', 'mail,engage_spark'))),
        OnboardedToPaidBuyerNotification::class => array_filter(explode(',', env('OnboardedToPaidBuyerNotification', 'mail,engage_spark'))),
        OnboardedToPaymentFailedBuyerNotification::class => array_filter(explode(',', env('OnboardedToPaymentFailedBuyerNotification', 'mail,engage_spark'))),
        OverriddenToCancelledBuyerNotification::class => array_filter(explode(',', env('OverriddenToCancelledBuyerNotification', 'mail,engage_spark'))),
        OverriddenToValidatedBuyerNotification::class => array_filter(explode(',', env('OverriddenToValidatedBuyerNotification', 'mail,engage_spark'))),
        PaidToAssignedBuyerNotification::class => array_filter(explode(',', env('PaidToAssignedBuyerNotification', 'mail,engage_spark'))),
        PaymentFailedToPaidBuyerNotification::class => array_filter(explode(',', env('PaymentFailedToPaidBuyerNotification', 'mail,engage_spark'))),
        PrequalifiedToNotQualifiedBuyerNotification::class => array_filter(explode(',', env('PrequalifiedToNotQualifiedBuyerNotification', 'mail,engage_spark'))),
        PrequalifiedToQualifiedBuyerNotification::class => array_filter(explode(',', env('PrequalifiedToQualifiedBuyerNotification', 'mail,engage_spark'))),
        QualifiedToApprovedBuyerNotification::class => array_filter(explode(',', env('QualifiedToApprovedBuyerNotification', 'mail,engage_spark'))),
        QualifiedToDisapprovedBuyerNotification::class => array_filter(explode(',', env('QualifiedToDisapprovedBuyerNotification', 'mail,engage_spark'))),
        ReuploadDocumentBuyerNotification::class => array_filter(explode(',', env('ReuploadDocumentBuyerNotification', 'mail,engage_spark'))),
        TestNotification::class => array_filter(explode(',', env('TestNotification', 'mail,engage_spark'))),
        ValidatedToCancelledBuyerNotification::class => array_filter(explode(',', env('ValidatedToCancelledBuyerNotification', 'mail,engage_spark'))),
        VerifiedToOnboardedBuyerNotification::class => array_filter(explode(',', env('VerifiedToOnboardedBuyerNotification', 'mail,engage_spark'))),
    ]
];
