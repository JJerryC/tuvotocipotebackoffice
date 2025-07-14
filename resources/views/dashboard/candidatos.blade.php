<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatos - Dashboard Electoral</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --dark-gradient: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            --accent-blue: #00d4ff;
            --accent-purple: #8b5cf6;
            --accent-green: #10b981;
            --accent-yellow: #f59e0b;
            --text-primary: #ffffff;
            --text-secondary: #b0b7c3;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-gradient);
            color: var(--text-primary);
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Background Effects */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
            text-decoration: none;
        }

        /* Filters Section */
        .filters-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .filters-header h3 {
            color: var(--text-primary);
            font-size: 1.3rem;
            font-weight: 600;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-group label {
            display: block;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }

        .form-control option {
            background: #1a1a2e;
            color: var(--text-primary);
        }

        .filter-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border: 1px solid var(--glass-border);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* Results Summary */
        .results-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .summary-stats {
            display: flex;
            gap: 2rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
        }

        /* Candidates Grid */
        .candidates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .candidate-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .candidate-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .candidate-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .candidate-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-blue);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
        }

        .candidate-info h4 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .candidate-id {
            color: var(--text-secondary);
            font-size: 0.8rem;
        }

        .candidate-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-presidencial {
            background: linear-gradient(45deg, #ffd700, #ff8c00);
            color: white;
        }

        .badge-diputado {
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-purple));
            color: white;
        }

        .badge-alcalde {
            background: linear-gradient(45deg, var(--accent-green), #06d6a0);
            color: white;
        }

        .badge-party {
            background: rgba(139, 92, 246, 0.2);
            color: var(--accent-purple);
            border: 1px solid var(--accent-purple);
        }

        .badge-independent {
            background: rgba(245, 158, 11, 0.2);
            color: var(--accent-yellow);
            border: 1px solid var(--accent-yellow);
        }

        .candidate-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .progress-section {
            margin-bottom: 1rem;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
            border-radius: 3px;
        }

        .candidate-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
        }

        .btn-info {
            background: var(--accent-blue);
            color: white;
        }

        .btn-warning {
            background: var(--accent-yellow);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
            grid-column: 1 / -1;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .pagination a,
        .pagination span {
            padding: 0.75rem 1rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: var(--primary-gradient);
            transform: translateY(-2px);
        }

        .pagination .active span {
            background: var(--primary-gradient);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .candidates-grid {
                grid-template-columns: 1fr;
            }

            .results-summary {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .filter-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-users"></i> Gestión de Candidatos
            </h1>
            <a href="{{ route('dashboard.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al Dashboard</span>
            </a>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                <h3><i class="fas fa-filter"></i> Filtros Avanzados</h3>
            </div>

            <form method="GET" action="{{ route('dashboard.candidatos') }}" class="filters-form">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="search">Búsqueda General</label>
                        <input type="text" name="search" id="search" class="form-control"
                               placeholder="Nombre, apellido o identidad..."
                               value="{{ request('search') }}">
                    </div>

                    <div class="filter-group">
                        <label for="tipo_candidato">Tipo de Candidato</label>
                        <select name="tipo_candidato" id="tipo_candidato" class="form-control">
                            <option value="">Todos los tipos</option>
                            <option value="presidencial" {{ request('tipo_candidato') == 'presidencial' ? 'selected' : '' }}>
                                Presidencial
                            </option>
                            <option value="diputado" {{ request('tipo_candidato') == 'diputado' ? 'selected' : '' }}>
                                Diputado
                            </option>
                            <option value="alcalde" {{ request('tipo_candidato') == 'alcalde' ? 'selected' : '' }}>
                                Alcalde
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="departamento_id">Departamento</label>
                        <select name="departamento_id" id="departamento_id" class="form-control">
                            <option value="">Todos los departamentos</option>
                            @foreach($departamentos as $dept)
                                <option value="{{ $dept->id }}" {{ request('departamento_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="municipio_id">Municipio</label>
                        <select name="municipio_id" id="municipio_id" class="form-control">
                            <option value="">Todos los municipios</option>
                            @foreach($municipios as $municipio)
                                <option value="{{ $municipio->id }}" {{ request('municipio_id') == $municipio->id ? 'selected' : '' }}>
                                    {{ $municipio->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="party_id">Partido Político</label>
                        <select name="party_id" id="party_id" class="form-control">
                            <option value="">Todos los partidos</option>
                            @foreach($partidos as $partido)
                                <option value="{{ $partido->id }}" {{ request('party_id') == $partido->id ? 'selected' : '' }}>
                                    {{ $partido->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="genero">Género</label>
                        <select name="genero" id="genero" class="form-control">
                            <option value="">Todos</option>
                            <option value="masculino" {{ request('genero') == 'masculino' ? 'selected' : '' }}>
                                Masculino
                            </option>
                            <option value="femenino" {{ request('genero') == 'femenino' ? 'selected' : '' }}>
                                Femenino
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="independiente">Candidatos Independientes</label>
                        <select name="independiente" id="independiente" class="form-control">
                            <option value="">Todos</option>
                            <option value="1" {{ request('independiente') == '1' ? 'selected' : '' }}>
                                Sí
                            </option>
                            <option value="0" {{ request('independiente') == '0' ? 'selected' : '' }}>
                                No
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="perfil_completo">Estado del Perfil</label>
                        <select name="perfil_completo" id="perfil_completo" class="form-control">
                            <option value="">Todos</option>
                            <option value="1" {{ request('perfil_completo') == '1' ? 'selected' : '' }}>
                                Completo
                            </option>
                            <option value="0" {{ request('perfil_completo') == '0' ? 'selected' : '' }}>
                                Incompleto
                            </option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('dashboard.candidatos') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Limpiar
                    </a>
                    <a href="{{ route('candidates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Candidato
                    </a>
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="results-summary">
            <div class="summary-stats">
                <div class="stat-item">
                    <i class="fas fa-users"></i>
                    <span>{{ $candidatos->total() }} candidatos encontrados</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Página {{ $candidatos->currentPage() }} de {{ $candidatos->lastPage() }}</span>
                </div>
            </div>
        </div>

        <!-- Candidates Grid -->
        <div class="candidates-grid">
            @forelse($candidatos as $candidato)
            <div class="candidate-card">
                <div class="candidate-header">
                    <img src="{{ $candidato->fotografia_url }}" alt="{{ $candidato->nombre_completo }}" class="candidate-photo">
                    <div class="candidate-info">
                        <h4>{{ $candidato->nombre_completo }}</h4>
                        <div class="candidate-id">ID: {{ $candidato->numero_identidad }}</div>
                    </div>
                </div>

                <div class="candidate-badges">
                    @if($candidato->tipo_candidato)
                        <span class="badge badge-{{ $candidato->tipo_candidato }}">
                            {{ ucfirst($candidato->tipo_candidato) }}
                        </span>
                    @endif

                    @if($candidato->independiente)
                        <span class="badge badge-independent">Independiente</span>
                    @elseif($candidato->party)
                        <span class="badge badge-party">{{ $candidato->party->name }}</span>
                    @endif
                </div>

                @if($candidato->departamento || $candidato->municipio)
                <div class="candidate-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>
                        {{ $candidato->departamento->name ?? '' }}
                        @if($candidato->municipio && $candidato->municipio->name !== 'Sin asignación')
                            , {{ $candidato->municipio->name }}
                        @endif
                    </span>
                </div>
                @endif

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Completitud del perfil</span>
                        <span>{{ $candidato->porcentaje_completado ?? 0 }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $candidato->porcentaje_completado ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="candidate-actions">
                    <a href="{{ route('candidates.show', $candidato->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                    <a href="{{ route('candidates.edit', $candidato->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-users-slash"></i>
                </div>
                <h3>No se encontraron candidatos</h3>
                <p>No hay candidatos que coincidan con los filtros aplicados.</p>
                <a href="{{ route('candidates.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Agregar Primer Candidato
                </a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($candidatos->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination">
                {{ $candidatos->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Departamento change handler
            document.getElementById('departamento_id').addEventListener('change', function() {
                const departamentoId = this.value;
                const municipioSelect = document.getElementById('municipio_id');

                // Limpiar municipios
                municipioSelect.innerHTML = '<option value="">Todos los municipios</option>';

                if (departamentoId) {
                    // Cargar municipios
                    fetch(`{{ route('dashboard.api.municipios', '') }}/${departamentoId}`)
                        .then(response => response.json())
                        .then(municipios => {
                            municipios.forEach(municipio => {
                                const option = document.createElement('option');
                                option.value = municipio.id;
                                option.textContent = municipio.name;
                                municipioSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error:', error));
                }
            });

            // Auto-submit on filter change
            const filterSelects = document.querySelectorAll('.filters-form select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    clearTimeout(this.filterTimeout);
                    this.filterTimeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            });

            // Search input handler
            const searchInput = document.getElementById('search');
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    this.closest('form').submit();
                }
            });
        });
    </script>
</body>
</html>
