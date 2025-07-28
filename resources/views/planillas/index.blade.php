@extends('adminlte::page')

@section('title', 'Planillas')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Planillas</h1>
@stop

@section('content')
    @if (session('success'))
        <x-adminlte-alert theme="success" title="Éxito">
            {{ session('success') }}
        </x-adminlte-alert>
    @endif

    @if (session('error'))
        <x-adminlte-alert theme="danger" title="Error">
            {{ session('error') }}
        </x-adminlte-alert>
    @endif

    <x-adminlte-card theme="primary" icon="fas fa-list" title="Planillas">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div></div>
            <div class="d-flex gap-2">
                <div id="buttons-container" class="me-2"></div>

                @can('create maintenance')
                    <a href="{{ route('planillas.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus mr-1"></i> Nuevo
                    </a>
                @endcan
            </div>
        </div>

        <table id="planillasTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Cargo</th>
                    <th>Departamento</th>
                    <th>Municipio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($planillas as $p)
                    <tr>
                        <td>
                            @if($p->foto)
                                <img src="{{ asset('storage/' . $p->foto) }}" width="150" class="img-thumbnail">
                            @endif
                        </td>
                        <td>{{ $p->nombre }}</td>
                        <td>{{ $p->cargo->name }}</td>
                        <td>{{ $p->departamento?->name }}</td>
                        <td>{{ $p->municipio?->name }}</td>
                        <td class="text-right">
                            @can('edit maintenance')
                                <a href="{{ route('planillas.edit', $p) }}" class="btn btn-xs btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            @can('delete maintenance')
                                <form action="{{ route('planillas.destroy', $p) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta planilla?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
        const table = $('#planillasTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                    className: 'btn btn-success btn-sm me-2',
                    exportOptions: {
                        columns: [1, 2, 3, 4] // columnas sin la foto ni acciones
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    exportOptions: {
                        columns: [1, 2, 3, 4]
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