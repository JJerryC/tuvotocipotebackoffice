@extends('adminlte::page')

@section('title','Usuarios')

@section('content_header')
    <h1 class="m-0 text-dark">Usuarios</h1>
@stop

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<x-adminlte-card>

    @can('create users')
        <div class="mb-3 text-right">
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-user-plus mr-1"></i> Nuevo
            </a>
        </div>
    @endcan

    <table id="usersTable" class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Roles</th>
                <th style="width: 100px">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>
                    @foreach($u->getRoleNames() as $role)
                        <span class="badge badge-info">{{ $role }}</span>
                    @endforeach
                </td>
                <td style="text-align: right;">
                    @can('edit users')
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-primary" title="Editar">
                            <i class="fas fa-user-cog"></i>
                        </a>
                    @endcan

                    @can('delete users')
                        @php
                            $semillaEmails = ['admin@example.com', 'test@example.com'];
                        @endphp
                        @if (!in_array($u->email, $semillaEmails))
                            <form action="{{ route('users.destroy', $u) }}" method="POST" style="display:inline-block"
                                onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        @endif
                    @endcan

                    @cannot('edit users')
                        @cannot('delete users')
                            &nbsp;
                        @endcannot
                    @endcannot
                </td>
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
    $('#usersTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });
});
</script>
@endpush
