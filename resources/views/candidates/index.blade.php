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

    @php
        $heads = ['ID', 'Nombre Completo', 'Partido', 'Municipio', 'Cargo', ['label' => 'Acciones', 'width' => 10]];
    @endphp

    <x-adminlte-datatable id="candidatesTable" :heads="$heads" hoverable striped>
        @foreach($candidates as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->nombre_completo }}</td>
                <td>{{ $c->party->name ?? '—' }}</td>
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
    </x-adminlte-datatable>

    <div class="mt-3">
        {{ $candidates->links() }}
    </div>
</x-adminlte-card>
@stop
