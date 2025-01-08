<?php

return [
    'end-points' => [
        'customer' => env('SHOW_CUSTOMER_ENDPOINT', 'http://homeful-contacts.test/api/references/:contact_reference_code'),
        'inventory' => env('SHOW_INVENTORY_ENDPOINT', 'https://properties.homeful.ph/api/next-property-details/:sku'),
        'register-contact' => env('REGISTER_CONTACT_ENDPOINT', 'http://homeful-contacts.test/register'),
        'api-register-contact' => env('REGISTER_CONTACT_ENDPOINT', 'http://homeful-contacts.test/api/register'),
        'matches' => env('MATCHES_ENDPOINT', 'http://homeful-match.test/api/match'),
        'verify-contact' => env('VERIFY_CONTACT_ENDPOINT', 'https://seqrcode.net/campaign-checkin/9de6ca3d-293f-4e98-8bde-daf13e1bfc91'),
        'collect-contact' => env('COLLECT_CONTACT_ENDPOINT', 'https://google.com'),
    ],
    'records-limit' => env('MATCHED_RECORDS_LIMIT', 3),
];
