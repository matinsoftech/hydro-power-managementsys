<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_level','parent_id','name','capacity','map_name','line_man_name','manager','latitude','longitude','created_by','start_date'
    ];

    public function parent()
    {
        return $this->belongsTo(Station::class, 'parent_id');
    }

    public function childs()
    {
        return $this->hasMany(Station::class, 'parent_id');
    }
}
