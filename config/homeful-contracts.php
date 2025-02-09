<?php

return [
    'end-points' => [
        'customer' => env('CUSTOMER_CONTACT','http://homeful-contacts.test/api/references/:contact_reference_code'),
        'inventory' => env('PROPERTY_SKU','https://properties.homeful.ph/api/next-property-details/:sku'),
        'product' => env('PRODUCT_SKU_ENDPOINT','https://properties.homeful.ph/api/product-details/:sku'),
        'register-contact' => env('REGISTER_CONTACT_ENDPOINT', 'http://homeful-contacts.test/register'),
        'api-register-contact' => env('REGISTER_API_CONTACT_ENDPOINT', 'http://homeful-contacts.test/api/register'),
        'matches' => env('MATCHES_ENDPOINT', 'http://homeful-match.test/api/match'),
        'verify-contact' => env('VERIFY_CONTACT_ENDPOINT', 'https://seqrcode.net/campaign-checkin/9de6ca3d-293f-4e98-8bde-daf13e1bfc91'),
        'collect-contact' => env('COLLECT_CONTACT_ENDPOINT', 'https://google.com'),
        'redeem-voucher' => env('REDEEM_VOUCHER_ENDPOINT', 'http://homeful-sellers.test/api/redeem/:voucher'),
    ],
    'records-limit' => env('MATCHED_RECORDS_LIMIT', 3),
    'test_homeful-id' => env('TEST_HOMEFUL_ID', 'H-KEF4SE')
];
