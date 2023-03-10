<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KpiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kpi_keys';

    protected $fillable = [
        'name',
        'description',
        'unit_id',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function targetLogs()
    {
        return $this
            ->belongsToMany(TargetLog::class, 'target_log_kpi_key', 'kpi_key_id', 'target_log_id')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
