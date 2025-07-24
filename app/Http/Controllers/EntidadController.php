<?php

namespace App\Http\Controllers;

use App\Models\Entidad;
use App\Models\Party;
use Illuminate\Http\Request;

class EntidadController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage candidates']);
    }

    /**
     * Mostrar listado de entidades.
     */
    
    public function index()
    {
        $entidades = Entidad::with('party')->orderBy('name')->get();
        return view('entidades.index', compact('entidades'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $parties = Party::orderBy('name')->pluck('name', 'id');
        return view('entidades.create', compact('parties'));
    }

    /**
     * Guardar una nueva entidad.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:entidades,name',
            'party_id' => 'required|exists:parties,id',
        ]);

        // Convertir a mayúsculas el nombre
        $data['name'] = mb_strtoupper($data['name'], 'UTF-8');

        Entidad::create($data);

        return redirect()->route('entidades.index')
                         ->with('success', 'Entidad creada correctamente');
    }

    /**
     * Mostrar detalles de una entidad.
     */
    public function show(Entidad $entidad)
    {
        return view('entidades.show', compact('entidad'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(Entidad $entidad)
    {
        $parties = Party::orderBy('name')->pluck('name', 'id');
        return view('entidades.edit', compact('entidad', 'parties'));
    }

    /**
     * Actualizar una entidad existente.
     */
    public function update(Request $request, Entidad $entidad)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:entidades,name,' . $entidad->id,
            'party_id' => 'required|exists:parties,id',
        ]);

        // Convertir a mayúsculas el nombre
        $data['name'] = mb_strtoupper($data['name'], 'UTF-8');

        $entidad->update($data);

        return redirect()->route('entidades.index')
                         ->with('success', 'Entidad actualizada correctamente');
    }

    /**
     * Eliminar una entidad.
     */
    public function destroy(Entidad $entidad)
    {
        try {
            $entidad->delete();
            return redirect()->route('entidades.index')
                            ->with('success', 'Entidad eliminada correctamente');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('entidades.index')
                            ->with('error', 'No se puede eliminar esta entidad porque tiene registros asociados.');
        }
    }
}
