<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Municipio extends Model
{
    use HasFactory;

    protected $table = 'municipios';
    protected $fillable = [
        'departamento_id',
        'code',
        'name',
    ];

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
