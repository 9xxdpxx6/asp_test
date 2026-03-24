<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactBranch extends Model
{
    protected $guarded = [];

    protected $casts = [
        'photos' => 'array',
    ];
}
