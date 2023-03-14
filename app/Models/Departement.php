<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departements';
    protected $fillable = [
        'name',
        'parent',
        'description',
        'salary_fund',
        'in_charge',
        'max_employees',
    ];

    // public function parent()
    // {
    //     return $this->belongsTo(Departement::class, 'parent');
    // }

    public function users() {
        return $this->hasMany(User::class, 'departement_id');
    }
}
