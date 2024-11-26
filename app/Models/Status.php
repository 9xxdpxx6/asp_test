<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;
    use Filterable;

    protected $table = 'statuses';
    protected $guarded = false;
    public function callbackRequests()
    {
        return $this->hasMany(CallbackRequest::class);
    }
}
