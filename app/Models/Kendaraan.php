<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'merk',
        'color'
    ];
}
