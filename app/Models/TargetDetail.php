<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TargetDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'target_details';

    protected $fillable = [
        'target_id',
        'user_id',
        'name',
        'description',
        'executionPlan',
        'manday',
        'quantity',
        'startDate',
        'deadline',
        'status',
        'managerComment',
        'managerManDay',
    ];

    protected $hidden = [
        'deleted_at'

    ];

    public function target()
    {
        return $this->belongsTo(Target::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function targetLogs()
    {
        return $this->hasMany(TargetLog::class);
    }
}
