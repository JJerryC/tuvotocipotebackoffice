@extends('adminlte::page')

@section('title', 'Entidades')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Entidades</h1>
@stop

@section('content')
    <x-adminlte-card theme="primary" icon="fas fa-building" title="Entidades">

        @can('manage candidates')
            <div class="mb-3 text-right">
                <a href="{{ route('entidades.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus mr-1"></i> Nuevo
                </a>
            </div>
        @endcan

        @php
            $heads = ['Nombre', 'Partido', 'Acciones'];
        @endphp

        <x-adminlte-datatable id="entidadesTable" :heads="$heads" hoverable striped>
            @foreach($entidades as $entidad)
            <tr>
                <td>{{ $entidad->name }}</td>
                <td>{{ $entidad->party->name ?? '—' }}</td>
                <td>
                    <a href="{{ route('entidades.edit', $entidad->id) }}" class="btn btn-xs btn-primary" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('entidades.destroy', $entidad->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta entidad?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-xs btn-danger" title="Eliminar">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </x-adminlte-datatable>
    </x-adminlte-card>
@stop