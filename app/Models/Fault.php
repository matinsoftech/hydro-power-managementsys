<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fault extends Model
{
    use HasFactory;

    protected $fillable = [
        'fault_time',
        'reason',
        'photo',
        'video',
        'status',
        'solved_by',
    ];

    public function solvedBy()
    {
        return $this->belongsTo(User::class,'solved_by');
    }
}
