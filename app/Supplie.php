<?php

namespace App;

use App\Manufacturer;
use Illuminate\Database\Eloquent\Model;

class Supplie extends Model
{
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturers_id');
    }
}
