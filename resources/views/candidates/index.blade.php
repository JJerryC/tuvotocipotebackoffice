@extends('adminlte::page')

@section('title', 'Candidatos')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Candidatos</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-users" title="Candidatos">
    <x-slot name="tools">
        {{-- Botón crear (opcional) --}}
        @can('manage candidates')
            <a href="{{ route('candidates.create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Nuevo
            </a>
        @endcan
    </x-slot>

    {{-- Tabla --}}
    @php
        $heads = ['ID', 'Nombre Completo', 'Partido', 'Municipio', 'Cargo', ['label' => 'Acciones', 'width' => 10]];
    @endphp

    <x-adminlte-datatable id="candidatesTable" :heads="$heads" hoverable striped>
        @foreach($candidates as $c)
            <tr>
                <td>{{ $c->id }}</td>
                <td>{{ $c->nombre_completo }}</td>
                <td>{{ $c->party->nombre ?? '—' }}</td>
                <td>{{ $c->municipio->nombre ?? '—' }}</td>
                <td>{{ $c->cargo->nombre ?? '—' }}</td>
                <td>
                    <a href="{{ route('candidates.edit', $c) }}" class="btn btn-xs btn-primary">
                        <i class="fas fa-edit"></i>
                    </a>
                    {{-- agrega destroy si lo necesitas --}}
                </td>
            </tr>
        @endforeach
    </x-adminlte-datatable>

    <div class="mt-3">
        {{ $candidates->links() }} {{-- paginación --}}
    </div>
</x-adminlte-card>
@stop
