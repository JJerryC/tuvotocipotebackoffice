@extends('adminlte::page')

@section('title', 'Candidatos')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Candidatos</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-users" title="Candidatos">

    @can('manage candidates')
        <div class="mb-3 text-right">
            <a href="{{ route('candidates.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Nuevo
            </a>
        </div>
    @endcan

    <table id="candidatesTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                {{-- <th>ID</th> --}} {{-- ID oculto --}}
                <th>Foto</th>
                <th>Nombre Completo</th>
                <th>Partido</th>
                <th>Departamento</th>
                <th>Municipio</th>
                <th>Cargo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($candidates as $c)
                <tr>
                    {{-- <td>{{ $c->id }}</td> --}}
                    <td>
                        @if($c->fotografia)
                            <img src="{{ asset('storage/' . $c->fotografia) }}" alt="Foto de {{ $c->nombre_completo }}" style="max-height: 50px; border-radius: 4px;">
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $c->nombre_completo }}</td>
                    <td>{{ $c->party->name ?? '—' }}</td>
                    <td>{{ $c->departamento->name ?? '—' }}</td>
                    <td>{{ $c->municipio->name ?? '—' }}</td>
                    <td>{{ $c->cargo->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('candidates.edit', $c) }}" class="btn btn-xs btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('candidates.destroy', $c) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este candidato?')">
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
    {{-- Estilos de DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endpush

@push('js')
    {{-- Scripts de DataTables --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#candidatesTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });
        });
    </script>
@endpush