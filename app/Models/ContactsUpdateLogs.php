<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Homeful\Contacts\Models\Contact;
class ContactsUpdateLogs extends Model
{
    protected $fillable = [
        'contacts_id',
        'field',
        'from',
        'to',
        'user_id'
    ];
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contacts_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
