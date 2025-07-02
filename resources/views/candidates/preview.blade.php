@extends('adminlte::page')

@section('title', 'Previsualizar Importación')

@section('content_header')
    <h1>Previsualizar Datos de Importación</h1>
@stop

@section('content')
<div class="container-fluid">
    @if(!empty($errors))
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Errores Encontrados</h5>
                    @foreach($errors as $sheetName => $sheetErrors)
                        <strong>{{ $sheetName }}:</strong>
                        <ul>
                            @foreach($sheetErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if(!empty($previewData))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Resumen de Importación</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php $totalCandidates = 0; @endphp
                            @foreach($previewData as $sheetName => $sheetData)
                                @php $totalCandidates += $sheetData['total']; @endphp
                                <div class="col-md-4">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">{{ $sheetName }}</span>
                                            <span class="info-box-number">{{ $sheetData['total'] }} candidatos</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total</span>
                                        <span class="info-box-number">{{ $totalCandidates }} candidatos</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <form action="{{ route('candidates.import.confirm') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('¿Está seguro de que desea importar todos los datos?')">
                                        <i class="fas fa-upload"></i> Confirmar Importación
                                    </button>
                                </form>
                                <a href="{{ route('candidates.import') }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach($previewData as $sheetName => $sheetData)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $sheetName }} - {{ $sheetData['total'] }} candidatos</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Fila</th>
                                            <th>Partido</th>
                                            <th>Nómina</th>
                                            <th>Departamento</th>
                                            <th>Municipio</th>
                                            <th>Cargo</th>
                                            <th>Pos.</th>
                                            <th>Identidad</th>
                                            <th>Nombre Completo</th>
                                            <th>Sexo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sheetData['candidates'] as $candidate)
                                            <tr>
                                                <td>{{ $candidate['row_number'] }}</td>
                                                <td>
                                                    <small class="text-muted">{{ Str::limit($candidate['partido'], 20) }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ Str::limit($candidate['nomina'], 25) }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $candidate['departamento'] ?: 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $candidate['municipio'] ?: 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ Str::limit($candidate['cargo'], 20) }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ $candidate['posicion'] }}</span>
                                                </td>
                                                <td>
                                                    <code>{{ $candidate['numero_identidad'] }}</code>
                                                </td>
                                                <td>
                                                    <strong>{{ $candidate['primer_nombre'] }} {{ $candidate['segundo_nombre'] }}</strong><br>
                                                    <small class="text-muted">{{ $candidate['primer_apellido'] }} {{ $candidate['segundo_apellido'] }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $candidate['sexo'] === 'HOMBRE' ? 'badge-primary' : 'badge-pink' }}">
                                                        {{ $candidate['sexo'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info"></i> Sin datos</h5>
                    No se encontraron datos válidos para importar.
                    <br><br>
                    <a href="{{ route('candidates.import') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Volver a intentar
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@stop

@section('css')
<style>
    .badge-pink {
        background-color: #e83e8c;
        color: white;
    }
    .table-sm td {
        padding: 0.3rem;
    }
</style>
@stop
