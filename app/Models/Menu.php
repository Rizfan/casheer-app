<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    //
    use HasUuids;
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
    ];
    protected $fillable = [
        'name',
        'image',
        'price',
    ];
}
