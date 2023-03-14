<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'position_levels';
    protected $fillable = [
        'name',
        'description',
        'minimum_wage',
        'maximum_wage',
    ];

    public function users() {
        return $this->hasMany(User::class, 'position_level_id');
    }
}
