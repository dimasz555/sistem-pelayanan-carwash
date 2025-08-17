<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappApiToken extends Model
{
    
    protected $table = 'whatsapp_api_token';

    protected $fillable = [
        'sender',
        'api_token',
        'url',
        'status',
    ];

}
