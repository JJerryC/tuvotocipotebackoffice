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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div></div>
        <div class="d-flex gap-2">
            <div id="buttons-container" class="me-2"></div>

            @can('create users')
            <a href="{{ route('users.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-user-plus mr-1"></i> Nuevo
            </a>
            @endcan
        </div>
    </div>

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
        const table = $('#usersTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    className: 'btn btn-success btn-sm me-2',
                    exportOptions: {
                        columns: [0, 1, 2, 3] // exportar ID, Nombre, Correo, Roles
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
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