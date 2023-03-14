<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Target extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'targets';

    protected $fillable = [
        'name',
        'description',
        'manday',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function targetDetails()
    {
        return $this->hasMany(TargetDetail::class);
    }
}
