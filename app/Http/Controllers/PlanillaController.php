<?php

namespace App\Http\Controllers;

use App\Models\Planilla;
use App\Models\Cargo;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlanillaController extends Controller
{
    public function index()
    {
        $planillas = Planilla::with(['cargo', 'departamento', 'municipio'])->get();
        return view('planillas.index', compact('planillas'));
    }

    public function create()
    {
        $cargos = Cargo::all();
        $departamentos = Departamento::all();
        return view('planillas.create', compact('cargos', 'departamentos'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cargo_id' => 'required|exists:cargos,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Convertir nombre a mayúsculas
        $data['nombre'] = strtoupper($data['nombre']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('planillas', 'public');
        }

        Planilla::create($data);

        return redirect()->route('planillas.index')->with('success', 'Planilla creada correctamente.');
    }

    public function edit(Planilla $planilla)
    {
        $cargos = Cargo::all();
        $departamentos = Departamento::all();
        $municipios = [];

        if ($planilla->departamento_id) {
            $municipios = Municipio::where('departamento_id', $planilla->departamento_id)->orderBy('name')->get();
        }

        return view('planillas.edit', compact('planilla', 'cargos', 'departamentos', 'municipios'));
    }
    
    public function update(Request $request, Planilla $planilla)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cargo_id' => 'required|exists:cargos,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'foto' => 'nullable|image|max:2048',
        ]);

        // Convertir nombre a mayúsculas
        $data['nombre'] = strtoupper($data['nombre']);

        if ($request->hasFile('foto')) {
            if ($planilla->foto) {
                Storage::disk('public')->delete($planilla->foto);
            }
            $data['foto'] = $request->file('foto')->store('planillas', 'public');
        }

        $planilla->update($data);

        return redirect()->route('planillas.index')->with('success', 'Planilla actualizada correctamente.');
    }
    public function destroy(Planilla $planilla)
    {
        if ($planilla->foto) Storage::disk('public')->delete($planilla->foto);
        $planilla->delete();

        return redirect()->route('planillas.index')->with('success', 'Planilla eliminada correctamente.');
    }
}