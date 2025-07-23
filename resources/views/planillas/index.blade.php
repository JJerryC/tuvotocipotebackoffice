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

        @can('create maintenance')
            <div class="mb-3 text-right">
                <a href="{{ route('planillas.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus mr-1"></i> Nuevo
                </a>
            </div>
        @endcan

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
                        <td>{{ $p->municipio?->name }}</td> {{-- Mostrar municipio --}}
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
@endpush

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(function() {
        $('#planillasTable').DataTable({
            responsive: true,
            pageLength: 10,
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' }
        });
    });
</script>
@endpush