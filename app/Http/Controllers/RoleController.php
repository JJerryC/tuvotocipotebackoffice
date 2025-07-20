<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Spatie\Permission\Models\{Role, Permission};

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /* ---------- Exportar confidenciales ---------- */
    public function exportConfidential()
    {
        $this->authorize('view confidential candidates');

        // Solo los usuarios con permiso acceden aquí
        $candidates = Candidate::where('is_confidential', true)->get();

        // Lógica de exportación a Excel (implementa según tu librería, ej. Laravel Excel)
        // return Excel::download(...);
    }

    /* ---------- LISTA ---------- */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    /* ---------- NUEVO ---------- */
    public function create()
    {
        // Agrupar los permisos por el segundo término (ej. 'edit users' → 'users')
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($perm) {
            return explode(' ', $perm->name)[1] ?? 'otros';
        });

        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_name'      => 'required|string|max:50|unique:roles,name',
            'permissions'    => 'nullable|array',
            'permissions.*'  => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $data['role_name']]);

        $perms = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        $role->syncPermissions($perms);

        return redirect()->route('roles.index')
                         ->with('success', 'Rol creado y permisos asignados');
    }

    /* ---------- EDITAR ---------- */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($perm) {
            return explode(' ', $perm->name)[1] ?? 'otros';
        });

        return view('roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $perms = Permission::whereIn('id', $data['permissions'] ?? [])->get();
        $role->syncPermissions($perms);

        return redirect()->route('roles.index')
                         ->with('success', 'Permisos actualizados');
    }

    public function destroy(Role $role)
    {
        // Impedir que se elimine el rol "admin"
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')->with('error', 'No puedes eliminar el rol admin.');
        }

        // Autorización con permiso
        $this->authorize('delete roles');

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }

}
