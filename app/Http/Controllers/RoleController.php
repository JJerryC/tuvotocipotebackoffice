<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Spatie\Permission\Models\{Role, Permission};

class RoleController extends Controller
{
    // Permisos fijos que no pueden quitarse del rol admin
    protected $adminFixedPermissions = [
        'view roles',
        'create roles',
        'edit roles',
        'delete roles',
        'manage roles',
    ];

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function exportConfidential()
    {
        $this->authorize('view confidential candidates');

        $candidates = Candidate::where('is_confidential', true)->get();

        // lógica exportación...
    }

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
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

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($perm) {
            return explode(' ', $perm->name)[1] ?? 'otros';
        });

        $adminFixed = $role->name === 'admin' ? $this->adminFixedPermissions : [];

        return view('roles.edit', compact('role', 'permissions', 'adminFixed'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permsIds = $data['permissions'] ?? [];

        if ($role->name === 'admin') {
            $fixedPermIds = Permission::whereIn('name', $this->adminFixedPermissions)->pluck('id')->toArray();
            $permsIds = array_unique(array_merge($permsIds, $fixedPermIds));
        }

        $perms = Permission::whereIn('id', $permsIds)->get();
        $role->syncPermissions($perms);

        return redirect()->route('roles.index')
                         ->with('success', 'Permisos actualizados');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')->with('error', 'No puedes eliminar el rol admin.');
        }

        $this->authorize('delete roles');

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente.');
    }
}
