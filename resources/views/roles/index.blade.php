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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <div class="d-flex gap-2">
            <div id="buttons-container" class="me-2"></div>

            @can('create roles')
            <a href="{{ route('roles.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Nuevo rol
            </a>
            @endcan
        </div>
    </div>

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
                <td>{{ $role->name }}</td>

                <td>
                    @forelse($role->permissions as $perm)
                        <span class="badge bg-info">{{ $perm->name }}</span>
                    @empty
                        <span class="text-muted">—</span>
                    @endforelse
                </td>

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
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

</x-adminlte-card>
@stop

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function () {
        const table = $('#rolesTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    className: 'btn btn-success btn-sm me-2',
                    exportOptions: {
                        columns: [0, 1] // exporta columnas Rol y Permisos
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [0, 1]
                    },
                    orientation: 'landscape',
                    pageSize: 'A4'
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            pageLength: 10,
        });

        table.buttons().container().appendTo('#buttons-container');
    });
</script>
@endpush