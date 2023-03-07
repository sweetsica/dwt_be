<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TargetLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'target_logs';

    protected $fillable = [
        'target_detail_id',
        'note',
        'quantity',
        'status',
        'files',
        'noticedStatus',
        'noticedDate',
        'reportedDate',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function targetDetail()
    {
        return $this->belongsTo(TargetDetail::class);
    }
}
