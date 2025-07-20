@extends('adminlte::page')

@section('title', 'Nóminas')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Nóminas</h1>
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

<x-adminlte-card theme="primary" icon="fas fa-list" title="Nóminas">

    @can('create maintenance')
        <div class="mb-3 text-right">
            <a href="{{ route('nominas.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Nuevo
            </a>
        </div>
    @endcan

    <table id="nominasTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nominas as $nomina)
                    <tr>
                        <td>{{ $nomina->name }}</td>
                        <td class="text-right">
                        @can('edit maintenance')
                            <a href="{{ route('nominas.edit', $nomina) }}" class="btn btn-xs btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endcan
                        @can('delete maintenance')
                            <form action="{{ route('nominas.destroy', $nomina) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('¿Eliminar esta nómina?')">
                                @csrf
                                @method('DELETE')
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
@endpush

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#nominasTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });
});
</script>
@endpush