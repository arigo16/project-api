<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kendaraan extends Model
{
    use SoftDeletes;

    // protected $table = 'kendaraan';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'merk',
        'color',
        'deleted_at'
    ];

    protected $hidden = [
        'deleted_at'
    ];
}
