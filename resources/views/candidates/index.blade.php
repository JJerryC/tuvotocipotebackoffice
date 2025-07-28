@extends('adminlte::page')

@section('title', 'Candidatos')

@section('content_header')
    <h1 class="m-0 text-dark">Listado de Candidatos</h1>
@stop

@section('content')
<x-adminlte-card theme="primary" icon="fas fa-users" title="Candidatos">

    <div class="d-flex justify-content-between align-items-center mb-3">
        {{-- Título opcional o espaciador --}}
        <div></div>

        <div class="d-flex gap-2">
            {{-- Contenedor para los botones de exportar --}}
            <div id="buttons-container" class="me-2"></div>

            {{-- Botón Nuevo (si tiene permiso) --}}
            @can('create candidates')
                <a href="{{ route('candidates.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus mr-1"></i> Nuevo
                </a>
            @endcan
        </div>
    </div>

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
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@push('js')
    {{-- Scripts de DataTables --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#candidatesTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                        className: 'btn btn-success btn-sm me-2', // Botón verde con texto blanco
                        exportOptions: {
                            columns: [1, 3, 4, 5, 6]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                        className: 'btn btn-danger btn-sm', // Botón rojo con texto blanco
                        exportOptions: {
                            columns: [1, 3, 4, 5, 6]
                        },
                        orientation: 'landscape',
                        pageSize: 'A4'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                }
            });

            table.buttons().container().appendTo('#buttons-container');
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