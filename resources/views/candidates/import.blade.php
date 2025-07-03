@extends('adminlte::page')

@section('title', 'Importar Candidatos')

@section('content_header')
    <h1>Importar Candidatos desde Excel</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Card de Subida de Archivo -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Subir Archivo Excel</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="icon fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-8">
                            <form id="uploadForm" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="excel_file">Seleccionar archivo Excel</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="excel_file" name="excel_file" accept=".xlsx,.xls" required>
                                            <label class="custom-file-label" for="excel_file">Elegir archivo...</label>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos soportados: .xlsx, .xls (máximo 10MB)
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-primary" id="previewBtn">
                                    <i class="fas fa-eye"></i> Procesar y Previsualizar
                                </button>
                                <button type="button" class="btn btn-danger ml-2" id="clearDatabaseBtn" title="Limpiar todas las tablas de candidatos">
                                    <i class="fas fa-trash-alt"></i> Limpiar Base de Datos
                                </button>
                                <div class="spinner-border spinner-border-sm ml-2 d-none" id="loadingSpinner" role="status">
                                    <span class="sr-only">Procesando...</span>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-info-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Información</span>
                                    <span class="info-box-number">
                                        <small>
                                            • Se mostrarán los primeros 100 registros<br>
                                            • Datos normalizados automáticamente<br>
                                            • Importación de todos los registros
                                        </small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas de procesamiento -->
            <div id="processAlert" class="d-none"></div>

            <!-- Botón de importación (se muestra después del preview) -->
            <div id="importSection" class="d-none">
                <div class="card">
                    <div class="card-body text-center">
                        <button type="button" class="btn btn-success btn-lg" id="confirmImportBtn">
                            <i class="fas fa-upload"></i> Confirmar Importación de Todos los Datos
                        </button>
                        <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="location.reload()">
                            <i class="fas fa-redo"></i> Cargar Otro Archivo
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sección de previsualización de datos -->
            <div id="previewSection" class="d-none">
                <!-- Aquí se cargarán los datos via AJAX -->
            </div>
        </div>
    </div>

    <!-- Mostrar datos existentes si hay -->
    @if(!empty($previewData))
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Datos Procesados - Previsualización (Primeros 100 registros)</h3>
                    </div>
                    <div class="card-body">
                        @php $totalCandidates = 0; @endphp
                        @foreach($previewData as $sheetName => $sheetData)
                            @php $totalCandidates += $sheetData['total']; @endphp
                        @endforeach
                        
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Resumen</h5>
                            Se procesaron <strong>{{ $totalCandidates }}</strong> registros para previsualización.
                            La importación incluirá todos los registros del archivo.
                        </div>

                        <div class="text-center mb-3">
                            <button type="button" class="btn btn-success btn-lg" onclick="confirmImport()">
                                <i class="fas fa-upload"></i> Confirmar Importación Completa
                            </button>
                            <button type="button" class="btn btn-secondary btn-lg ml-2" onclick="location.reload()">
                                <i class="fas fa-redo"></i> Cargar Otro Archivo
                            </button>
                        </div>

                        @foreach($previewData as $sheetName => $sheetData)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h4 class="card-title">{{ $sheetName }} - {{ $sheetData['total'] }} registros 
                                        @if(isset($sheetData['limited']) && $sheetData['limited'])
                                            <small class="text-muted">(mostrando primeros 100)</small>
                                        @endif
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-sm">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Fila</th>
                                                    <th>Partido</th>
                                                    <th>Nómina</th>
                                                    <th>Depto</th>
                                                    <th>Municipio</th>
                                                    <th>Cargo</th>
                                                    <th>Pos</th>
                                                    <th>Identidad</th>
                                                    <th>Nombre Completo</th>
                                                    <th>Sexo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sheetData['candidates'] as $candidate)
                                                    <tr>
                                                        <td><small>{{ $candidate['row_number'] }}</small></td>
                                                        <td><small class="text-muted">{{ Str::limit($candidate['partido'], 15) }}</small></td>
                                                        <td><small class="text-muted">{{ Str::limit($candidate['nomina'], 20) }}</small></td>
                                                        <td><small class="text-muted">{{ Str::limit($candidate['departamento'], 10) ?: 'Nacional' }}</small></td>
                                                        <td><small class="text-muted">{{ Str::limit($candidate['municipio'], 10) ?: 'Nacional' }}</small></td>
                                                        <td><small class="text-muted">{{ Str::limit($candidate['cargo'], 15) }}</small></td>
                                                        <td><span class="badge badge-info">{{ $candidate['posicion'] }}</span></td>
                                                        <td><code style="font-size: 0.8em;">{{ $candidate['numero_identidad'] }}</code></td>
                                                        <td>
                                                            <strong style="font-size: 0.9em;">{{ $candidate['primer_nombre'] }} {{ $candidate['segundo_nombre'] }}</strong><br>
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
                        @endforeach
                    </div>
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
        padding: 0.2rem;
        font-size: 0.85em;
    }
    .table-sm th {
        padding: 0.3rem;
        font-size: 0.8em;
    }
</style>
@stop

<!-- Modal de Confirmación para Limpiar Base de Datos -->
<div class="modal fade" id="clearDatabaseModal" tabindex="-1" role="dialog" aria-labelledby="clearDatabaseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="clearDatabaseModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> ⚠️ Confirmar Limpieza de Base de Datos
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>¡ATENCIÓN! Esta acción es IRREVERSIBLE</strong>
                </div>
                
                <p><strong>Se eliminarán TODOS los datos de las siguientes tablas:</strong></p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-users text-primary"></i> <strong>Candidatos</strong> - Todos los registros de candidatos</li>
                    <li><i class="fas fa-flag text-success"></i> <strong>Partidos</strong> - Todos los partidos políticos</li>
                    <li><i class="fas fa-building text-info"></i> <strong>Entidades</strong> - Todas las entidades</li>
                    <li><i class="fas fa-list text-warning"></i> <strong>Nóminas</strong> - Todas las nóminas</li>
                    <li><i class="fas fa-map text-secondary"></i> <strong>Departamentos</strong> - Todos los departamentos</li>
                    <li><i class="fas fa-map-marker-alt text-danger"></i> <strong>Municipios</strong> - Todos los municipios</li>
                    <li><i class="fas fa-briefcase text-dark"></i> <strong>Cargos</strong> - Todos los cargos</li>
                </ul>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> Se mantendrán los datos básicos de sexos (M, F, O) y se recrearán los registros "Sin asignación" por defecto.
                </div>
                
                <p><strong>¿Está completamente seguro de que desea continuar?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmClearBtn">
                    <i class="fas fa-trash-alt"></i> Sí, Limpiar Todo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Progreso de Importación -->
<div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="progressModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="progressModalLabel">
                    <i class="fas fa-upload"></i> Importando Candidatos
                </h5>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <h4 id="progressTitle">Inicializando importación...</h4>
                    <p class="text-muted" id="progressSubtitle">Por favor espera mientras procesamos los datos</p>
                </div>
                
                <!-- Barra de progreso -->
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                         role="progressbar" 
                         style="width: 0%" 
                         id="progressBar">
                        <span id="progressText">0%</span>
                    </div>
                </div>
                
                <!-- Estadísticas -->
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total</span>
                                <span class="info-box-number" id="totalRecords">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Procesados</span>
                                <span class="info-box-number" id="processedRecords">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Exitosos</span>
                                <span class="info-box-number" id="successRecords">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Errores</span>
                                <span class="info-box-number" id="errorRecords">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Log de progreso -->
                <div class="mt-3">
                    <h6><i class="fas fa-list-alt"></i> Log de Progreso:</h6>
                    <div id="progressLog" style="height: 150px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 0.9em;">
                        <div class="text-muted">Iniciando proceso de importación...</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="progressFooter">
                <button type="button" class="btn btn-secondary" disabled id="closeProgressBtn">
                    <i class="fas fa-times"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    // Actualizar el nombre del archivo seleccionado
    $('#excel_file').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });

    // Manejar el formulario de subida
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        // Mostrar loading
        $('#previewBtn').prop('disabled', true);
        $('#loadingSpinner').removeClass('d-none');
        $('#processAlert').addClass('d-none');
        
        $.ajax({
            url: '{{ route("candidates.preview") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Mostrar mensaje de éxito
                    $('#processAlert').removeClass('d-none').html(`
                        <div class="alert alert-success">
                            <i class="fas fa-check"></i> ${response.message}
                        </div>
                    `);
                    
                    // Mostrar sección de importación
                    $('#importSection').removeClass('d-none');
                    
                    // Recargar la página para mostrar los datos
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    // Mostrar error
                    $('#processAlert').removeClass('d-none').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> ${response.message}
                        </div>
                    `);
                }
            },
            error: function(xhr) {
                let message = 'Error al procesar el archivo';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                
                $('#processAlert').removeClass('d-none').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${message}
                    </div>
                `);
            },
            complete: function() {
                $('#previewBtn').prop('disabled', false);
                $('#loadingSpinner').addClass('d-none');
            }
        });
    });

    // Función para confirmar importación con progreso
    function confirmImport() {
        if (!confirm('¿Está seguro de que desea importar TODOS los datos del archivo? Esta acción no se puede deshacer.')) {
            return;
        }

        // Mostrar modal de progreso
        $('#progressModal').modal('show');
        addToProgressLog('🚀 Iniciando proceso de importación...');

        // Iniciar importación
        $.ajax({
            url: '{{ route("candidates.start-import") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json',
            },
            success: function(response) {
                addToProgressLog(`📊 Total de registros: ${response.total}`);
                addToProgressLog(`📦 Tamaño de lote: ${response.batch_size}`);
                
                $('#totalRecords').text(response.total);
                
                // Iniciar procesamiento por lotes
                processImportBatches(response.session_id);
            },
            error: function(xhr) {
                let message = 'Error al iniciar la importación';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    message = xhr.responseJSON.error;
                }
                addToProgressLog(`❌ Error: ${message}`);
                $('#closeProgressBtn').prop('disabled', false);
            }
        });
    }

    // Procesar lotes de importación
    function processImportBatches(sessionId) {
        function processBatch() {
            $.ajax({
                url: `/candidates/import-batch/${sessionId}`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Actualizar progreso
                    updateProgressDisplay(response);
                    
                    if (response.status === 'completed') {
                        // Importación completada
                        addToProgressLog('✅ ¡Importación completada exitosamente!');
                        $('#progressTitle').text('¡Importación Completada!');
                        $('#progressSubtitle').text('Todos los registros han sido procesados');
                        $('#progressBar').removeClass('progress-bar-animated');
                        $('#closeProgressBtn').prop('disabled', false).removeClass('btn-secondary').addClass('btn-success');
                    } else {
                        // Continuar con el siguiente lote
                        addToProgressLog(`📈 Procesados: ${response.processed} registros (${response.percentage}%)`);
                        setTimeout(processBatch, 500); // Pausa de 500ms entre lotes
                    }
                },
                error: function(xhr) {
                    addToProgressLog(`❌ Error en lote: ${xhr.responseJSON ? xhr.responseJSON.error : 'Error desconocido'}`);
                    $('#closeProgressBtn').prop('disabled', false);
                }
            });
        }
        
        processBatch();
    }

    // Actualizar display de progreso
    function updateProgressDisplay(data) {
        $('#processedRecords').text(data.processed || 0);
        $('#successRecords').text(data.success || 0);
        $('#errorRecords').text(data.errors || 0);
        
        const percentage = data.percentage || 0;
        $('#progressBar').css('width', percentage + '%');
        $('#progressText').text(percentage.toFixed(1) + '%');
    }

    // Agregar mensaje al log de progreso
    function addToProgressLog(message) {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = `<div class="mb-1">[${timestamp}] ${message}</div>`;
        $('#progressLog').append(logEntry);
        
        // Scroll automático al final
        const logContainer = document.getElementById('progressLog');
        logContainer.scrollTop = logContainer.scrollHeight;
    }

    // Botón de confirmación de importación
    $('#confirmImportBtn').on('click', confirmImport);
    
    // Cerrar modal y recargar página cuando se complete
    $('#closeProgressBtn').on('click', function() {
        $('#progressModal').modal('hide');
        location.reload();
    });

    // Manejar botón de limpiar base de datos
    $('#clearDatabaseBtn').on('click', function() {
        $('#clearDatabaseModal').modal('show');
    });

    // Confirmar limpieza de base de datos
    $('#confirmClearBtn').on('click', function() {
        const btn = $(this);
        const originalText = btn.html();
        
        // Deshabilitar botón y mostrar loading
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Limpiando...');
        
        $.ajax({
            url: '{{ route("candidates.clear-database") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#clearDatabaseModal').modal('hide');
                    
                    // Mostrar mensaje de éxito
                    let message = '✅ Base de datos limpiada exitosamente!\n\n';
                    message += 'Registros eliminados:\n';
                    for (const [table, count] of Object.entries(response.cleared_counts)) {
                        message += `• ${table}: ${count} registros\n`;
                    }
                    
                    alert(message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                let message = 'Error al limpiar la base de datos';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            },
            complete: function() {
                // Restaurar botón
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
</script>
@stop
