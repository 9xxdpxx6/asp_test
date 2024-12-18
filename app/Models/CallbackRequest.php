<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallbackRequest extends Model
{
    use HasFactory;
    use Filterable;

    protected $table = 'callback_requests';
    protected $guarded = false;
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
