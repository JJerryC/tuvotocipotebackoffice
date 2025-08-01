@extends('adminlte::page')

@section('title', 'Candidatos - Dashboard Electoral')

@section('content_header')
    <h1><i class="fas fa-users"></i> Gestión de Candidatos</h1>
@stop

@section('content')
    <div class="filters-section mb-4">
        <form method="GET" action="{{ route('dashboard.candidatos') }}" class="filters-form">
            <div class="filter-grid">
                <div class="filter-group">
                    <label for="search">Búsqueda General</label>
                    <input type="text" name="search" id="search" class="form-control"
                        placeholder="Nombre, Identidad, Partido ..."
                        value="{{ request('search') }}">
                </div>
            </div>

            <div class="filter-actions mt-3 d-flex justify-content-end">
                <a href="{{ route('dashboard.candidatos') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-times"></i> Limpiar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <div class="results-summary d-flex justify-content-between mb-4">
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
            <div class="card glass-card">
                <div class="card-body">
                    <div class="candidate-header d-flex align-items-center mb-3">
                        <img 
                        src="{{ $candidato->fotografia ? asset('storage/' . $candidato->fotografia) : asset('images/default-candidate.png') }}" 
                        alt="{{ $candidato->nombre_completo }}" 
                        class="candidate-photo rounded-circle me-3" 
                        style="width:80px; height:80px; object-fit:cover; border:3px solid var(--accent-blue);">
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

                        @if($candidato->tipo_candidato)
                            <span class="badge badge-type badge-{{ strtolower($candidato->tipo_candidato) }}">
                                {{ ucfirst($candidato->tipo_candidato) }}
                            </span>
                        @elseif($candidato->party)
                        <span class="badge badge-party d-flex align-items-center gap-2" style="white-space: nowrap;">
                            @if($candidato->party->foto_partido)
                                <img src="{{ asset('storage/' . $candidato->party->foto_partido) }}" 
                                    alt="Bandera {{ $candidato->party->name }}" 
                                    class="img-thumbnail img-party-large">
                            @endif
                            <span style="margin-left: 0.5rem;"> <!-- Ajusta el valor a tu gusto -->
                                {{ $candidato->party->name }}
                            </span>
                        </span>
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

                    <div class="candidate-actions d-flex justify-content-end gap-2">
                        <a href="{{ route('candidates.show', $candidato->id) }}" class="btn btn-info btn-sm btn-glass">
                            <i class="fas fa-eye"></i> Ver
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
        </div>
        @endforelse
    </div>

    @if($candidatos->hasPages())
    <div class="pagination-wrapper d-flex justify-content-center mt-4">
        <ul class="pagination glass-pagination">
            @if ($candidatos->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item"><a class="page-link" href="{{ $candidatos->previousPageUrl() }}" rel="prev">&laquo;</a></li>
            @endif

            @foreach ($candidatos->getUrlRange(1, $candidatos->lastPage()) as $page => $url)
                @if ($page == $candidatos->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            @if ($candidatos->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $candidatos->nextPageUrl() }}" rel="next">&raquo;</a></li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
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
        /* Gradientes y colores para modo claro */
        --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
        --secondary-gradient: linear-gradient(135deg, #a5b4fc 0%, #818cf8 100%);
        --dark-gradient: linear-gradient(135deg, #f3f4f6 0%, #e0e7ff 100%); /* Fondo claro */
        --accent-blue: #3b82f6;
        --accent-purple: #8b5cf6;
        --accent-pink: #ec4899;
        --accent-green: #10b981;
        --accent-yellow: #f59e0b;
        --text-primary: #1f2937;   /* Texto oscuro para legibilidad */
        --text-secondary: #4b5563; /* Texto secundario gris oscuro */
        --glass-bg: rgba(255, 255, 255, 0.8);  /* Fondo glass más claro */
        --glass-border: rgba(209, 213, 219, 0.6); /* Borde glass claro */
    }
.img-party-large {
    max-width: 60px;
    max-height: 36px;
}
    /* Fondos claros en body y contenido */
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
        background:
            radial-gradient(circle at 20% 80%, rgba(147, 197, 253, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(244, 114, 182, 0.3) 0%, transparent 50%);
        pointer-events: none !important;
        z-index: -1 !important;
    }

    /* Tarjetas con efecto vidrio claro */
    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        border: 1px solid var(--glass-border);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        transition: 0.3s;
        color: var(--text-primary);
    }

    /* Sección filtros con glass claro */
    .filters-section {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: var(--text-primary);
    }

    /* Grid para filtros */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
        gap: 1.5rem;
    }

    /* Etiquetas con texto secundario oscuro */
    .filter-group label {
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* Inputs y selects con fondo claro y texto oscuro */
    .filter-group input.form-control,
    .filter-group select.form-control {
        background: rgba(255,255,255,0.9);
        border: 1px solid rgba(209, 213, 219, 0.6);
        color: var(--text-primary);
        border-radius: 8px;
        padding: 0.4rem 0.75rem;
        transition: border-color 0.3s;
        box-shadow: none;
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
        background: #fff;
        color: var(--text-primary);
    }

    /* Botones con gradientes y colores claros */
    .btn-primary {
        background: var(--accent-blue);
        border: none;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.7);
        transition: background 0.3s ease;
        color: white;
    }
    .btn-primary:hover {
        background: #2563eb; /* azul más oscuro al pasar el mouse */
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.8);
        color: white;
    }

    .btn-secondary {
        background: transparent;
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
    }
    .btn-secondary:hover {
        background: var(--accent-blue);
        color: #fff;
        border-color: var(--accent-blue);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.5);
    }

    .btn-success {
        background: var(--accent-green);
        border: none;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.7);
        color: white;
    }
    .btn-success:hover {
        background: #0f9e69;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.9);
        color: white;
    }

    /* Botones glass con texto oscuro */
    .btn-glass {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(15px);
        border-radius: 8px;
        border: 1px solid rgba(209, 213, 219, 0.6);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }
    .btn-info.btn-glass:hover {
        background: #3b82f6 !important; /* Azul sólido */
        color: white !important;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.7) !important;
    }

    /* Badges con colores vivos y texto blanco */
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
        box-shadow: 0 4px 10px rgba(79, 70, 229, 0.7);
    }
    .badge-independent {
        background: var(--accent-pink);
        box-shadow: 0 4px 10px rgba(236, 72, 153, 0.7);
    }
    .badge-party {
        background: var(--accent-blue);
        color: #000 !important;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4); /* sombra suave azul */
    }

    /* Nombre de candidato con gradiente y texto transparente para efecto */
    .candidate-name {
        color: var(--text-primary);
        font-weight: 700;
    }

    /* Iconos de ubicación con acento azul */
    .candidate-location i {
        margin-right: 6px;
        color: var(--accent-blue);
    }

    /* Barra de progreso (si la usas) con gradiente primario */
    .progress-bar.bg-primary {
        background: var(--primary-gradient) !important;
    }

    /* Texto en estado vacío con color secundario */
    .empty-state {
        color: var(--text-secondary);
    }

    /* Estilo para resumen de resultados (número de candidatos y página) */
    .results-summary {
        background: var(--glass-bg);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1);
        color: var(--text-primary);
        font-weight: 600;
        font-size: 1rem;
    }
    .results-summary i {
        color: var(--accent-blue);
        margin-right: 0.5rem;
    }

    /* Paginación con estilo glass claro */
    .glass-pagination {
        background: var(--glass-bg);
        border-radius: 12px;
        padding: 0.5rem 1rem;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        list-style: none;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .glass-pagination .page-item {
        display: inline-block;
    }

    .glass-pagination .page-link {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(209, 213, 219, 0.6);
        color: var(--text-primary);
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        transition: 0.2s;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .glass-pagination .page-link:hover {
        background: var(--accent-blue);
        color: #fff;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.5);
    }

    .glass-pagination .page-item.active .page-link {
        background: var(--accent-blue); /* azul sólido */
        color: #fff;
        pointer-events: none;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.8);
    }

    .glass-pagination .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .candidate-card .card {
        min-height: 280px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .candidate-header {
        /* Evita que el header crezca demasiado */
        flex-shrink: 0;
    }

    /* Opcional: limita altura del nombre para evitar que sea muy alto */
    .candidate-name {
        max-height: 3em;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: normal;
    }

    /* Para que el área de acciones quede siempre abajo */
    .candidate-actions {
        margin-top: auto;
    }

    .party-flag-img {
    width: 20px;
    height: 14px;
    object-fit: cover;
    border-radius: 3px;
    border: 1px solid rgba(0,0,0,0.1);
    box-shadow: 0 0 3px rgba(0,0,0,0.1);
    }

</style>
@stop