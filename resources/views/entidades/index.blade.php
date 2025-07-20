@extends('adminlte::page')

@section('title', 'Entidades')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Entidades</h1>
@stop

@section('content')

@if(session('success'))
    <x-adminlte-alert theme="success" title="Éxito">
        {{ session('success') }}
    </x-adminlte-alert>
@endif

@if(session('error'))
    <x-adminlte-alert theme="danger" title="Error">
        {{ session('error') }}
    </x-adminlte-alert>
@endif

<x-adminlte-card theme="primary" icon="fas fa-building" title="Entidades">

    @can('create maintenance')
        <div class="mb-3 text-right">
            <a href="{{ route('entidades.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Nuevo
            </a>
        </div>
    @endcan

    <table id="entidadesTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Partido</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entidades as $entidad)
            <tr>
                <td>{{ $entidad->name }}</td>
                <td>{{ $entidad->party->name ?? '—' }}</td>
                <td class="text-right">
                @can('edit maintenance')
                    <a href="{{ route('entidades.edit', $entidad) }}" class="btn btn-xs btn-primary" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                @endcan
                    @php
                        $tieneParty = $entidad->party()->exists();
                    @endphp

                    @if ($tieneParty)
                        <!-- La entidad tiene partido, se puede eliminar -->
                    @can('delete maintenance')
                        <form action="{{ route('entidades.destroy', $entidad) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta entidad?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-danger" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    @endcan
                    @else
                        <!-- La entidad no tiene partido, bloqueo botón -->
                        <button class="btn btn-xs btn-secondary" title="Entidad sin partido asignado" disabled>
                            <i class="fas fa-ban"></i>
                        </button>
                    @endif
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
    $('#entidadesTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
    });
});
</script>
@endpush