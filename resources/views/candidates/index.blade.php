@extends('adminlte::page')

@section('title', 'Candidatos')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Candidatos</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-users" title="Candidatos">

    @can('create candidates')
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
                <th>Bandera</th>
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
<!-- Logo -->
<td class="text-center align-middle">
    @if($c->party && $c->party->foto_partido)
        <img 
            src="{{ asset('storage/' . $c->party->foto_partido) }}" 
            alt="Logo {{ $c->party->name }}" 
            title="{{ $c->party->name }}"
            style="height: 30px; width: auto; border-radius: 4px;">
    @else
        —
    @endif
</td>

<!-- Nombre del Partido -->
<td class="align-middle">
    {{ $c->party->name ?? '—' }}
</td>
                    <td>{{ $c->departamento->name ?? '—' }}</td>
                    <td>{{ $c->municipio->name ?? '—' }}</td>
                    <td>{{ $c->cargo->name ?? '—' }}</td>
                    <td class="text-right">
                        @can('edit candidates')
                            <a href="{{ route('candidates.edit', $c) }}" class="btn btn-xs btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endcan

                        @can('delete candidates')
                            <form action="{{ route('candidates.destroy', $c) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('¿Eliminar este candidato?')">
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


    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Busca el link en el menú que tiene texto 'Modo Oscuro'
        const btn = document.querySelector('a.btn-dark-mode');

        if (!btn) return;

        const darkClass = 'dark-mode';

        // Inicializa estado
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add(darkClass);
            btn.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
        } else {
            btn.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
        }

        btn.addEventListener('click', e => {
            e.preventDefault();

            if (document.body.classList.contains(darkClass)) {
                document.body.classList.remove(darkClass);
                localStorage.setItem('darkMode', 'disabled');
                btn.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
            } else {
                document.body.classList.add(darkClass);
                localStorage.setItem('darkMode', 'enabled');
                btn.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
            }
        });
    });
</script>
@endpush