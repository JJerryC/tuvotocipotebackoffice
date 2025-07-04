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

    @php
        $heads = ['Nombre', ['label' => 'Acciones', 'width' => 10]];
    @endphp

    <x-adminlte-datatable id="cargosTable" :heads="$heads" hoverable striped>
        @foreach($cargos as $cargo)
        <tr>
            <td>{{ $cargo->name }}</td>
            <td>
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
    </x-adminlte-datatable>
</x-adminlte-card>
@stop