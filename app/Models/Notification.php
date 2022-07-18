<?php

namespace App\Models;

use App\Events\NotificationCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    protected $dispatchesEvents = [
        'created' => NotificationCreated::class,
    ];
}
