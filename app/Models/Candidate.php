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
        'planilla_id',
        'posicion',
        'numero_identidad',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fotografia',
        'fotografia_original',
        'reeleccion',
        'propuestas',
        'tipo_candidato',
        'genero',
        'independiente',
        'porcentaje_completado',
        'perfil_completo',
        'ocupacion',
    ];

    protected $casts = [
        'reeleccion' => 'boolean',
        'independiente' => 'boolean',
        'perfil_completo' => 'boolean',
        'porcentaje_completado' => 'integer'
    ];

    /* Relaciones existentes */
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

    public function planilla()
    {
    return $this->belongsTo(Planilla::class);
    }

    /* Accessor para nombre completo - YA EXISTE */
    public function getNombreCompletoAttribute(): string
    {
        return collect([
            $this->primer_nombre,
            $this->segundo_nombre,
            $this->primer_apellido,
            $this->segundo_apellido,
        ])->filter()->join(' ');
    }

    /* NUEVOS ACCESSORS PARA EL DASHBOARD */

    // Accessor para obtener la URL de la fotografía
    protected $hidden = ['fotografia'];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        return $this->fotografia ? asset('storage/' . $this->fotografia) : asset('images/default-avatar.png');
    }

    // Scopes para filtros del dashboard
    public function scopeTipoCandidato($query, $tipo)
    {
        return $query->where('tipo_candidato', $tipo);
    }

    public function scopeDepartamento($query, $departamento)
    {
        return $query->where('departamento_id', $departamento);
    }

    public function scopePartido($query, $partidoId)
    {
        return $query->where('party_id', $partidoId);
    }

    public function scopeGenero($query, $genero)
    {
        return $query->where('genero', $genero);
    }

    public function scopeIndependientes($query)
    {
        return $query->where('independiente', true);
    }

    public function scopePerfilCompleto($query, $completo = true)
    {
        return $query->where('perfil_completo', $completo);
    }

    // Método para calcular porcentaje de completado
    public function calcularPorcentajeCompletado()
    {
        $campos = [
            'primer_nombre',
            'primer_apellido',
            'fotografia',
            'propuestas',
            'tipo_candidato',
            'numero_identidad'
        ];

        $completados = 0;

        foreach ($campos as $campo) {
            if (!empty($this->$campo)) {
                $completados++;
            }
        }

        $porcentaje = ($completados / count($campos)) * 100;
        $this->porcentaje_completado = round($porcentaje);
        $this->perfil_completo = $porcentaje >= 80;

        return $this->porcentaje_completado;
    }

    // Método para determinar el tipo de candidato basado en el cargo
    public function determinarTipoCandidato()
    {
        if (!$this->cargo) return null;

        $cargo = strtolower($this->cargo->name);

        if (str_contains($cargo, 'presidente') || str_contains($cargo, 'presidencial')) {
            return 'presidencial';
        } elseif (str_contains($cargo, 'diputado') || str_contains($cargo, 'congreso')) {
            return 'diputado';
        } elseif (str_contains($cargo, 'alcalde') || str_contains($cargo, 'municipal')) {
            return 'alcalde';
        }

        return 'otro';
    }

    // Método para determinar género basado en sexo
    public function determinarGenero()
    {
        if (!$this->sexo) return null;

        $codigo = strtoupper($this->sexo->code);

        if ($codigo === 'M' || $codigo === 'H') {
            return 'masculino';
        } elseif ($codigo === 'F') {
            return 'femenino';
        }

        return null;
    }

    // Método para actualizar campos automáticamente
    public function actualizarCamposAutomaticos()
    {
        // Determinar tipo de candidato si no está definido
        if (!$this->tipo_candidato) {
            $this->tipo_candidato = $this->determinarTipoCandidato();
        }

        // Determinar género si no está definido
        if (!$this->genero) {
            $this->genero = $this->determinarGenero();
        }

        // Determinar si es independiente
        $this->independiente = $this->party_id === null;

        // Calcular porcentaje de completado
        $this->calcularPorcentajeCompletado();

        $this->save();
    }
}
