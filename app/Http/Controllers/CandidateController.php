<?php

namespace App\Http\Controllers;

use App\Models\{
    Candidate, Entidad, Party, Nomina,
    Departamento, Municipio, Cargo, Sexo
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage candidates']);
    }

    public function index()
    {
        $candidates = Candidate::with(['entidad', 'party', 'municipio', 'cargo', 'departamento'])
            ->orderByDesc('id')
            ->paginate(15);

        return view('candidates.index', compact('candidates'));
    }

    public function create()
    {
        return view('candidates.create', [
            'entidades'     => Entidad::orderBy('name')->pluck('name', 'id'),
            'parties'       => Party::orderBy('name')->pluck('name', 'id'),
            'nominas'       => Nomina::orderBy('name')->pluck('name', 'id'),
            'departamentos' => Departamento::orderBy('name')->pluck('name', 'id'),
            'municipios'    => Municipio::orderBy('name')->pluck('name', 'id'),
            'cargos'        => Cargo::orderBy('name')->pluck('name', 'id'),
            'sexos'         => Sexo::orderBy('description')->pluck('description', 'id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'independiente'    => 'nullable|boolean',

            'party_id'         => 'required_if:independiente,false|nullable|exists:parties,id',
            'entidad_id'       => 'required_if:independiente,false|nullable|exists:entidades,id',

            'nomina_id'        => 'required|exists:nominas,id',
            'departamento_id'  => 'required|exists:departamentos,id',
            'municipio_id'     => 'nullable|exists:municipios,id',
            'cargo_id'         => 'required|exists:cargos,id',
            'sexo_id'          => 'required|exists:sexos,id',
            'posicion'         => 'required|integer|min:0',
            'numero_identidad' => 'required|string|max:25|unique:candidates,numero_identidad',
            'primer_nombre'    => 'required|string|max:60',
            'segundo_nombre'   => 'nullable|string|max:60',
            'primer_apellido'  => 'required|string|max:60',
            'segundo_apellido' => 'nullable|string|max:60',
            'fotografia'       => 'nullable|image|max:2048',
            'reeleccion'       => 'nullable|boolean',
            'propuestas'       => 'nullable|string|max:5000',
        ]);

        if ($request->hasFile('fotografia')) {
            $file = $request->file('fotografia');
            $data['fotografia'] = $file->store('candidatos', 'public');
            $data['fotografia_original'] = $file->getClientOriginalName();
        }

        $data['reeleccion'] = $request->has('reeleccion');
        $data['independiente'] = $request->has('independiente');

        if ($data['independiente']) {
            $data['party_id'] = null;
            $data['entidad_id'] = null;
        }

        Candidate::create($data);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidato creado correctamente');
    }

    public function edit(Candidate $candidate)
    {
        return view('candidates.edit', [
            'candidate'     => $candidate,
            'entidades'     => Entidad::orderBy('name')->pluck('name', 'id'),
            'parties'       => Party::orderBy('name')->pluck('name', 'id'),
            'nominas'       => Nomina::orderBy('name')->pluck('name', 'id'),
            'departamentos' => Departamento::orderBy('name')->pluck('name', 'id'),
            'municipios'    => Municipio::orderBy('name')->pluck('name', 'id'),
            'cargos'        => Cargo::orderBy('name')->pluck('name', 'id'),
            'sexos'         => Sexo::orderBy('description')->pluck('description', 'id'),
        ]);
    }

    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'independiente'    => 'nullable|boolean',

            'party_id'         => 'required_if:independiente,false|nullable|exists:parties,id',
            'entidad_id'       => 'required_if:independiente,false|nullable|exists:entidades,id',

            'nomina_id'        => 'required|exists:nominas,id',
            'departamento_id'  => 'required|exists:departamentos,id',
            'municipio_id'     => 'nullable|exists:municipios,id',
            'cargo_id'         => 'required|exists:cargos,id',
            'sexo_id'          => 'required|exists:sexos,id',
            'posicion'         => 'required|integer|min:0',
            'numero_identidad' => [
                'required', 'string', 'max:25',
                Rule::unique('candidates', 'numero_identidad')->ignore($candidate->id),
            ],
            'primer_nombre'    => 'required|string|max:60',
            'segundo_nombre'   => 'nullable|string|max:60',
            'primer_apellido'  => 'required|string|max:60',
            'segundo_apellido' => 'nullable|string|max:60',
            'fotografia'       => 'nullable|image|max:2048',
            'reeleccion'       => 'nullable|boolean',
            'propuestas'       => 'nullable|string|max:5000',
        ]);

        if ($request->hasFile('fotografia')) {
            if ($candidate->fotografia && Storage::disk('public')->exists($candidate->fotografia)) {
                Storage::disk('public')->delete($candidate->fotografia);
            }

            $file = $request->file('fotografia');
            $data['fotografia'] = $file->store('candidatos', 'public');
            $data['fotografia_original'] = $file->getClientOriginalName();
        }

        $data['reeleccion'] = $request->has('reeleccion');
        $data['independiente'] = $request->has('independiente');

        if ($data['independiente']) {
            $data['party_id'] = null;
            $data['entidad_id'] = null;
        }

        $candidate->update($data);

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidato actualizado correctamente');
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->fotografia && Storage::disk('public')->exists($candidate->fotografia)) {
            Storage::disk('public')->delete($candidate->fotografia);
        }

        $candidate->delete();

        return redirect()
            ->route('candidates.index')
            ->with('success', 'Candidato eliminado correctamente');
    }

    public function show(Candidate $candidate)
    {
        $candidate->load([
            'party',
            'entidad',
            'nomina',
            'departamento',
            'municipio',
            'cargo',
            'sexo',
        ]);

        return view('candidates.show', compact('candidate'));
    }

    public function getEntidadesByParty($partyId)
    {
        $entidades = Entidad::where('party_id', $partyId)->orderBy('name')->get(['id', 'name']);
        return response()->json($entidades);
    }

    public function getMunicipiosByDepartamento($departamentoId)
    {
        $municipios = Municipio::where('departamento_id', $departamentoId)->orderBy('name')->get(['id', 'name']);
        return response()->json($municipios);
    }

}
