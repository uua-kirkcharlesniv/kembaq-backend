<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = ['subscription_id', 'user_id', 'merchant_id', 'value', 'running_balance', 'sender_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
}
