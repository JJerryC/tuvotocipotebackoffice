<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'foto_partido',
        'color_partido',
        'descripcion'
    ];

    /* Relaciones existentes */
    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function entidades()
    {
        return $this->hasMany(Entidad::class);
    }

    /* NUEVOS MÉTODOS PARA EL DASHBOARD */

    // Candidatos por tipo
    public function candidatosPresidenciales()
    {
        return $this->candidates()->where('tipo_candidato', 'presidencial');
    }

    public function candidatosDiputados()
    {
        return $this->candidates()->where('tipo_candidato', 'diputado');
    }

    public function candidatosAlcaldes()
    {
        return $this->candidates()->where('tipo_candidato', 'alcalde');
    }


    protected $appends = ['foto_partido_url'];
    protected $hidden = ['foto_partido'];

    // Accessor para obtener la URL de la foto del partido
    public function getFotoPartidoUrlAttribute()
    {
        return $this->foto_partido ? asset('storage/' . $this->foto_partido) : asset('images/default-party.png');
    }

    // Método para obtener estadísticas completas del partido
    public function getEstadisticas()
    {
        $candidatos = $this->candidates();

        return [
            'total_candidatos' => $candidatos->count(),
            'presidenciales' => $this->candidatosPresidenciales()->count(),
            'diputados' => $this->candidatosDiputados()->count(),
            'alcaldes' => $this->candidatosAlcaldes()->count(),
            'mujeres' => $candidatos->where('genero', 'femenino')->count(),
            'hombres' => $candidatos->where('genero', 'masculino')->count(),
            'perfiles_completos' => $candidatos->where('perfil_completo', true)->count(),
            'perfiles_incompletos' => $candidatos->where('perfil_completo', false)->count(),
            'porcentaje_completado_promedio' => $candidatos->avg('porcentaje_completado') ?? 0,
        ];
    }

    // Candidatos por departamento
    public function candidatosPorDepartamento()
    {
        return $this->candidates()
            ->join('departamentos', 'candidates.departamento_id', '=', 'departamentos.id')
            ->selectRaw('departamentos.name as departamento, COUNT(*) as total')
            ->groupBy('departamentos.id', 'departamentos.name')
            ->get();
    }
}
