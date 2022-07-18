<?php

namespace App\Models;

use App\Events\MessageCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function merchant() {
        return $this->belongsTo(Merchant::class);
    }

    public function getPhotoAttribute($value) {
        if($value == null || $value == '') {
            return 'https://reactnative-examples.com/wp-content/uploads/2022/02/default-loading-image.png';
        } else if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
            return asset('storage/'.$value);
        }

        return $value;
    }

    protected $dispatchesEvents = [
        'created' => MessageCreated::class,
    ];
}
