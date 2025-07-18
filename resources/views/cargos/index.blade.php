@extends('adminlte::page')

@section('title', 'Cargos')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Cargos</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-briefcase" title="Cargos">

    @can('manage candidates')
        <div class="mb-3 text-right">
            <a href="{{ route('cargos.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Nuevo
            </a>
        </div>
    @endcan

    <table id="cargosTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cargos as $cargo)
            <tr>
                <td>{{ $cargo->name }}</td>
                <td class="text-right">
                    <a href="{{ route('cargos.edit', $cargo) }}" class="btn btn-xs btn-primary" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('cargos.destroy', $cargo) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('¿Eliminar este cargo?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
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
            $('#cargosTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });
    </script>
@endpush