<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Planilla extends Model
{
    use HasFactory;

    protected $table = 'planillas';

    protected $fillable = [
        'nombre',
        'foto',
        'cargo_id',
        'departamento_id',
        'municipio_id',
    ];


    protected $appends = ['foto_url'];

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : asset('images/default-avatar.png');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }
}
