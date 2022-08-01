<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];

    protected $appends = ['expires_at'];

    public function getExpiresAtAttribute() {
        $start = $this->created_at;

        switch ($this->type) {
            case 0:
                return $start->addDays(7);
                break;
            case 1:
                return $start->addMonths(1);
                break;
            case 2:
                return $start->addYears(1);
                break;
            default:
                break;
        }
    }
}
