<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'party_id',
        'entidad_id',
        'nomina_id',
        'departamento_id',
        'municipio_id',
        'cargo_id',
        'sexo_id',
        'posicion',
        'numero_identidad',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fotografia',
        'reeleccion',
        'planes_propuestas',
    ];

    protected $casts = [
        'reeleccion' => 'boolean',
    ];

    /* Relaciones */
    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function entidad()
    {
        return $this->belongsTo(Entidad::class);
    }

    public function nomina()
    {
        return $this->belongsTo(Nomina::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function sexo()
    {
        return $this->belongsTo(Sexo::class);
    }

    /* Accessor para nombre completo */
    public function getNombreCompletoAttribute(): string
    {
        return collect([
            $this->primer_nombre,
            $this->segundo_nombre,
            $this->primer_apellido,
            $this->segundo_apellido,
        ])->filter()->join(' ');
    }
}
