<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;

class Merchant extends Model implements Wallet
{
    use HasFactory, HasWallet;

    protected $guarded = ['id'];

    public function category() {
        return $this->belongsTo(Category::class, 'category', 'id');
    }
}
