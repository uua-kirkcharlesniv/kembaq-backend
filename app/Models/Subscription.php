<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'merchant_id', 'balance'];

    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
}
