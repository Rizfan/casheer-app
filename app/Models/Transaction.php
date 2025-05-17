<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    //
    use HasUuids;
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
    ];

    protected $fillable = [
        'total_price',
        'amount_paid',
        'change_return',
        'status',
        'transaction_details',
    ];
    protected $casts = [
        'transaction_details' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
