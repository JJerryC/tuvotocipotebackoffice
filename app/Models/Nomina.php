<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas';
    protected $fillable = [
        'entidad_id',
        'name',
    ];

    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
