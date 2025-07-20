@extends('adminlte::page')

@section('title','Roles')

@section('content_header')
    <h1 class="m-0 text-dark">Roles</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<x-adminlte-card>

    @can('create roles')
    <div class="mb-3 text-right">
        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-success">
            <i class="fas fa-plus mr-1"></i> Nuevo rol
        </a>
    </div>
    @endcan

    <table id="rolesTable" class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Rol</th>
                <th>Permisos</th>
                <th style="width: 90px">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td> <!-- Columna Rol -->

                <td>
                    @forelse($role->permissions as $perm)
                        <span class="badge bg-info">{{ $perm->name }}</span>
                    @empty
                        <span class="text-muted">—</span>
                    @endforelse
                </td> <!-- Columna Permisos -->

                <td class="text-right">
                    @can('edit roles')
                    <a href="{{ route('roles.edit',$role) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    @endcan
                    
                    @can('delete roles')
                        @if($role->name !== 'admin')
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este rol?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    @endcan
                </td> <!-- Columna Acciones -->
            </tr>
        @endforeach
        </tbody>
    </table>

</x-adminlte-card>
@stop

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#rolesTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });
});
</script>
@endpush