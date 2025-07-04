<?php

namespace App\Http\Controllers;

use App\Models\{Candidate, Party, Nomina, Departamento, Municipio, Cargo, Sexo};
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    public function __construct()
    {
        // Protege todas las acciones con Spatie (permiso “manage candidates”)
        $this->middleware(['auth', 'permission:manage candidates']);
    }

    /* ========== LISTAR CANDIDATOS ========== */
    public function index()
    {
        $candidates = Candidate::with(['party','municipio','cargo'])
            ->orderBy('id','desc')
            ->paginate(15);

        return view('candidates.index', compact('candidates'));
    }

    /* ========== FORMULARIO NUEVO ========== */
    public function create()
    {
        return view('candidates.create', [
            'parties' => Party::orderBy('name')->pluck('name', 'id'),
            'nominas' => Nomina::orderBy('name')->pluck('name', 'id'),
            'departamentos' => Departamento::orderBy('name')->pluck('name', 'id'),
            'municipios' => Municipio::orderBy('name')->pluck('name', 'id'),
            'cargos' => Cargo::orderBy('name')->pluck('name', 'id'),
            'sexos' => Sexo::orderBy('description')->pluck('description', 'id'),
        ]);
    }

    /* ========== GUARDAR NUEVO ========== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'party_id' => 'required|exists:parties,id',
            'nomina_id' => 'required|exists:nominas,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'cargo_id' => 'required|exists:cargos,id',
            'sexo_id' => 'required|exists:sexos,id',
            'posicion' => 'required|integer|min:1',
            'numero_identidad' => 'required|string|max:25|unique:candidates,numero_identidad',
            'primer_nombre' => 'required|string|max:60',
            'segundo_nombre' => 'nullable|string|max:60',
            'primer_apellido' => 'required|string|max:60',
            'segundo_apellido' => 'nullable|string|max:60',
        ]);

        Candidate::create($data);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidato creado correctamente');
    }

    /* ========== FORMULARIO EDITAR ========== */
    public function edit(Candidate $candidate)
    {
        return view('candidates.edit', [
            'candidate' => $candidate,
            'parties' => Party::orderBy('name')->pluck('name', 'id'),
            'nominas' => Nomina::orderBy('name')->pluck('name', 'id'),
            'departamentos' => Departamento::orderBy('name')->pluck('name', 'id'),
            'municipios' => Municipio::orderBy('name')->pluck('name', 'id'),
            'cargos' => Cargo::orderBy('name')->pluck('name', 'id'),
            'sexos' => Sexo::orderBy('description')->pluck('description', 'id'),
        ]);
    }

    /* ========== ACTUALIZAR DATOS ========== */
    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'party_id' => 'required|exists:parties,id',
            'nomina_id' => 'required|exists:nominas,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'cargo_id' => 'required|exists:cargos,id',
            'sexo_id' => 'required|exists:sexos,id',
            'posicion' => 'required|integer|min:1',
            'numero_identidad' => 'required|string|max:25|unique:candidates,numero_identidad,' . $candidate->id,
            'primer_nombre' => 'required|string|max:60',
            'segundo_nombre' => 'nullable|string|max:60',
            'primer_apellido' => 'required|string|max:60',
            'segundo_apellido' => 'nullable|string|max:60',
        ]);

        $candidate->update($data);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidato actualizado correctamente');
    }

    /* ========== ELIMINAR ========== */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidato eliminado correctamente');
    }
}