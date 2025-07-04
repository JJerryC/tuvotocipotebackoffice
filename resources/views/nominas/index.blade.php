@extends('adminlte::page')

@section('title', 'Nóminas')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Nóminas</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-list" title="Nóminas">

    @can('manage candidates')
        <div class="mb-3 text-right">
            <a href="{{ route('nominas.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Nuevo
            </a>
        </div>
    @endcan

    @php
        $heads = ['Nombre', 'Entidad', ['label' => 'Acciones', 'width' => 10]];
    @endphp

    <x-adminlte-datatable id="nominasTable" :heads="$heads" hoverable striped>
        @foreach($nominas as $nomina)
        <tr>
            <td>{{ $nomina->name }}</td>
            <td>{{ $nomina->entidad->name ?? '—' }}</td>
            <td>
                <a href="{{ route('nominas.edit', $nomina) }}" class="btn btn-xs btn-primary" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('nominas.destroy', $nomina) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('¿Eliminar esta nómina?')">
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
</x-adminlte-card>
@stop