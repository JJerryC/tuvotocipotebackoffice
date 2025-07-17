@extends('adminlte::page')

@section('title', 'Candidatos - Dashboard Electoral')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Candidatos</h1>
@stop

@section('content')
    <div class="filters-section mb-4">
        <form method="GET" action="{{ route('dashboard.candidatos') }}" class="filters-form">
            <div class="filter-grid">
                <!-- Ejemplo filtro de búsqueda general -->
                <div class="filter-group">
                    <label for="search">Búsqueda General</label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="Nombre, apellido o identidad..."
                        value="{{ request('search') }}">
                </div>
                <!-- Añade aquí el resto de tus filtros -->
            </div>

            <div class="filter-actions mt-3 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
                <a href="{{ route('dashboard.candidatos') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Limpiar
                </a>
                <a href="{{ route('candidates.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nuevo Candidato
                </a>
            </div>
        </form>
    </div>

    <div class="results-summary d-flex justify-content-between mb-4 text-white">
        <div>
            <i class="fas fa-users"></i> {{ $candidatos->total() }} candidatos encontrados
        </div>
        <div>
            <i class="fas fa-chart-pie"></i> Página {{ $candidatos->currentPage() }} de {{ $candidatos->lastPage() }}
        </div>
    </div>

    <div class="candidates-grid row">
        @forelse($candidatos as $candidato)
        <div class="candidate-card col-md-4 mb-4 p-3">
            <div class="card bg-dark text-white glass-card">
                <div class="card-body">
                    <div class="candidate-header d-flex align-items-center mb-3">
                        <img src="{{ $candidato->fotografia_url }}" alt="{{ $candidato->nombre_completo }}" 
                            class="candidate-photo rounded-circle me-3" style="width:80px; height:80px; object-fit:cover; border:3px solid var(--accent-blue);">
                        <div>
                            <h4 class="candidate-name">{{ $candidato->nombre_completo }}</h4>
                            <div class="candidate-id text-secondary">ID: {{ $candidato->numero_identidad }}</div>
                        </div>
                    </div>

                    <div class="candidate-badges mb-3">
                        @if($candidato->tipo_candidato)
                            <span class="badge badge-type badge-{{ strtolower($candidato->tipo_candidato) }}">
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
                    <div class="candidate-location mb-3 text-secondary">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>
                            {{ $candidato->departamento->name ?? '' }}
                            @if($candidato->municipio && $candidato->municipio->name !== 'Sin asignación')
                                , {{ $candidato->municipio->name }}
                            @endif
                        </span>
                    </div>
                    @endif

                    <div class="candidate-actions d-flex gap-2">
                        <a href="{{ route('candidates.show', $candidato->id) }}" class="btn btn-info btn-sm btn-glass">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('candidates.edit', $candidato->id) }}" class="btn btn-warning btn-sm btn-glass">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state text-center col-12 py-5 text-secondary">
            <i class="fas fa-users-slash fa-4x mb-3"></i>
            <h3>No se encontraron candidatos</h3>
            <p>No hay candidatos que coincidan con los filtros aplicados.</p>
            <a href="{{ route('candidates.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus"></i> Agregar Primer Candidato
            </a>
        </div>
        @endforelse
    </div>

    @if($candidatos->hasPages())
    <div class="pagination-wrapper d-flex justify-content-center mt-4">
        {{ $candidatos->appends(request()->query())->links() }}
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('departamento_id');
            const municipioSelect = document.getElementById('municipio_id');

            departamentoSelect?.addEventListener('change', function() {
                const departamentoId = this.value;
                municipioSelect.innerHTML = '<option value="">Todos los municipios</option>';
                if (departamentoId) {
                    fetch(`/dashboard/api/municipios/${departamentoId}`)
                        .then(res => res.json())
                        .then(data => {
                            data.forEach(m => {
                                const option = document.createElement('option');
                                option.value = m.id;
                                option.textContent = m.name;
                                municipioSelect.appendChild(option);
                            });
                        });
                }
            });

            document.querySelectorAll('.filters-form select').forEach(select => {
                select.addEventListener('change', function() {
                    clearTimeout(this.filterTimeout);
                    this.filterTimeout = setTimeout(() => {
                        this.closest('form').submit();
                    }, 500);
                });
            });

            document.getElementById('search')?.addEventListener('keyup', function(e) {
                if(e.key === 'Enter') {
                    this.closest('form').submit();
                }
            });
        });
    </script>
@stop

@section('css')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
        --secondary-gradient: linear-gradient(135deg,#f093fb 0%,#f5576c 100%);
        --dark-gradient: linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);
        --accent-blue: #00d4ff;
        --accent-purple: #8b5cf6;
        --accent-pink: #ec4899;
        --accent-green: #10b981;
        --accent-yellow: #f59e0b;
        --text-primary: #ffffff;
        --text-secondary: #b0b7c3;
        --glass-bg: rgba(255,255,255,0.05);
        --glass-border: rgba(255,255,255,0.1);
    }

body, html, .content-wrapper, .wrapper {
    background: var(--dark-gradient) !important;
    min-height: 100vh !important;
    color: var(--text-primary) !important;
    position: relative !important;
}

body::before {
    content: '';
    position: fixed !important;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: radial-gradient(circle at 20% 80%, rgba(120,119,198,0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255,119,198,0.3) 0%, transparent 50%) !important;
    pointer-events: none !important;
    z-index: -1 !important;
}

    /* Glassmorphism general */
    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: 0.3s;
    }

    .filters-section {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: var(--text-primary);
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
        gap: 1.5rem;
    }
    .filter-group label {
        color: var(--text-secondary);
        font-weight: 500;
    }
    .filter-group input.form-control,
    .filter-group select.form-control {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.25);
        color: var(--text-primary);
        border-radius: 8px;
        padding: 0.4rem 0.75rem;
        transition: border-color 0.3s;
    }
    .filter-group input.form-control::placeholder {
        color: var(--text-secondary);
        opacity: 0.8;
    }
    .filter-group input.form-control:focus,
    .filter-group select.form-control:focus {
        outline: none;
        border-color: var(--accent-blue);
        box-shadow: 0 0 8px var(--accent-blue);
        background: rgba(255,255,255,0.18);
        color: var(--text-primary);
    }

    /* Botones */
    .btn-primary {
        background: var(--primary-gradient);
        border: none;
        box-shadow: 0 4px 15px rgba(102,126,234,0.7);
        transition: background 0.3s ease;
    }
    .btn-primary:hover {
        background: var(--secondary-gradient);
        box-shadow: 0 6px 20px rgba(240,147,251,0.8);
    }
    .btn-secondary {
        background: transparent;
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
    }
    .btn-secondary:hover {
        background: var(--glass-bg);
        color: var(--accent-purple);
        border-color: var(--accent-purple);
    }
    .btn-success {
        background: var(--accent-green);
        border: none;
        box-shadow: 0 4px 15px rgba(16,185,129,0.7);
    }
    .btn-success:hover {
        background: #0f9e69;
        box-shadow: 0 6px 20px rgba(16,185,129,0.9);
    }
    .btn-glass {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(15px);
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.2);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }
    .btn-glass:hover {
        background: var(--primary-gradient);
        color: #fff;
        box-shadow: 0 8px 25px var(--primary-gradient);
    }

    /* Badges */
    .badge {
        font-weight: 600;
        padding: 0.3em 0.8em;
        border-radius: 20px;
        font-size: 0.85rem;
        text-transform: capitalize;
        display: inline-block;
        margin-right: 0.4rem;
        transition: background 0.3s;
        color: #fff;
    }
    .badge-type {
        background: var(--primary-gradient);
        box-shadow: 0 4px 10px rgba(102,126,234,0.7);
    }
    .badge-independent {
        background: var(--accent-pink);
        box-shadow: 0 4px 10px rgba(236,72,153,0.7);
    }
    .badge-party {
        background: var(--accent-purple);
        box-shadow: 0 4px 10px rgba(139,92,246,0.7);
    }

    /* Candidate name */
    .candidate-name {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 700;
    }

    /* Candidate location icon */
    .candidate-location i {
        margin-right: 6px;
        color: var(--accent-blue);
    }

    /* Progress bar */
    .progress-bar.bg-primary {
        background: var(--primary-gradient) !important;
    }

    /* Empty state */
    .empty-state {
        color: var(--text-secondary);
    }
</style>
@stop
