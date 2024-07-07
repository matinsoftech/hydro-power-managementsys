<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grid extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'gone_time',
        'charge_time',
        'unit_one_sync_time',
        'unit_two_sync_time',
        'created_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}
