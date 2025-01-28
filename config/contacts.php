<?php

use Homeful\Contacts\Database\Factories\ContactFactory;

return [
    'models' => [
        'contact' => [
            'connection' => 'contacts-mysql',
            'table' => 'contacts',
            'factory_class' => ContactFactory::class
        ]
    ]
];
