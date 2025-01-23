<?php

namespace App\Models;

use Homeful\Contacts\Models\Contact as BaseContact;

class Contact extends BaseContact
{
    protected $connection = 'contacts-mysql';
    protected $table = 'contacts';
}
