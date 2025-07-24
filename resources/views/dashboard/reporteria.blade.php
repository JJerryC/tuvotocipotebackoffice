@extends('adminlte::page')

@section('title', 'Reportería Electoral - TuVoto Cipote')

@section('content_header')
    <h1><i class="fas fa-chart-line"></i> Reportería Electoral</h1>
@stop

@section('content')
<div class="filters-section mb-4 glass-card">
    <div class="container-fluid px-0">
        <!-- Summary Cards -->
<div class="summary-grid mb-4">
    <div class="summary-card presidencial">
        <div class="summary-icon">
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="summary-number">{{ $estadisticas['candidatos_presidenciales'] ?? 0 }}</div>
        <div class="summary-label">Candidatos Presidenciales</div>
    </div>

<div class="summary-card diputados">
    <div class="summary-icon">
        <i class="fas fa-building"></i>  <!-- Balanza de justicia para diputados -->
    </div>
    <div class="summary-number">{{ $estadisticas['candidatos_diputados'] ?? 0 }}</div>
    <div class="summary-label">Candidatos a Diputados</div>
</div>

    <div class="summary-card alcaldes">
        <div class="summary-icon" >
            <i class="fas fa-landmark"></i>  <!-- Edificio tipo parlamento para alcaldes -->
        </div>
        <div class="summary-number">{{ $estadisticas['candidatos_alcaldes'] ?? 0 }}</div>
        <div class="summary-label">Candidatos a Alcaldes</div>
    </div>

    <div class="summary-card">
        <div class="summary-icon">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="summary-number">{{ $estadisticas['total_candidatos'] ?? 0 }}</div>
        <div class="summary-label">Total de Candidatos</div>
    </div>
</div>

        <!-- Time Period Selector -->
        <div class="time-selector mb-4">
            <button class="time-btn active" data-period="7">Últimos 7 días</button>
            <button class="time-btn" data-period="30">Últimos 30 días</button>
            <button class="time-btn" data-period="90">Últimos 3 meses</button>
            <button class="time-btn" data-period="365">Último año</button>
            <button class="time-btn" data-period="all">Todo el tiempo</button>
        </div>

        <div class="reports-grid">
            <!-- Timeline Container -->
            <div class="timeline-container glass-card">
                <div class="timeline-header text-center mb-4">
                    <h3>Evolución de Registros de Candidatos</h3>
                    <p class="timeline-subtitle">Línea de tiempo con registros por día/semana</p>
                </div>

                <div class="chart-wrapper">
                    <canvas id="timelineChart"></canvas>
                </div>
            </div>

            <!-- Stats Container -->
            <div class="stats-container d-flex flex-column gap-4">
                <!-- Completitud Stats -->
                <div class="stat-card glass-card">
                    <div class="stat-card-header d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-tasks"></i>
                        <span class="stat-card-title">Perfiles Completados</span>
                    </div>

                    @foreach($estadisticas['completitud_perfiles'] as $estado)
    <div class="progress-item mb-3">
        <div class="progress-header d-flex justify-content-between mb-1">
            <span class="progress-label">{{ $estado['estado'] }}</span>
            <span class="progress-value">{{ $estado['total'] }}</span>
        </div>
        <div class="progress" style="height: 8px; border-radius: 5px; background: rgba(255,255,255,0.1);">
            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($estado['total'] / $estadisticas['por_departamento']->sum('total')) * 100 }}%"></div>
        </div>
    </div>
@endforeach
                </div>

                <!-- Top Departments -->
                <div class="stat-card glass-card">
                    <div class="stat-card-header d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-trophy"></i>
                        <span class="stat-card-title">Top Departamentos</span>
                    </div>

                    @foreach($estadisticas['por_departamento']->groupBy('departamento')->map(fn($dept) => $dept->sum('total'))->sortDesc()->take(5) as $departamento => $total)
                    <div class="department-item d-flex justify-content-between align-items-center py-2 border-bottom border-white-10">
                        <span class="department-name">{{ $departamento }}</span>
                        <span class="department-count badge badge-pill bg-primary">{{ $total }}</span>
                    </div>
                    @endforeach
                </div>

                <!-- Partidos Stats -->
@foreach($estadisticas['por_partido']->take(5) as $partidoData)
    @php
        $partido = data_get($partidoData, 'partido');
        $totalCandidatos = data_get($partidoData, 'estadisticas.total_candidatos', 0);
    @endphp

    <div class="progress-item mb-3">
        <div class="progress-header d-flex justify-content-between mb-1">
            <span class="progress-label">{{ $partido ? $partido->name : 'Partido no definido' }}</span>
            <span class="progress-value">{{ $totalCandidatos }}</span>
        </div>
        <div class="progress" style="height: 8px; border-radius: 5px; background: rgba(255,255,255,0.1);">
            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalCandidatos > 0 ? ($totalCandidatos / $estadisticas['por_departamento']->sum('total')) * 100 : 0 }}%"></div>
        </div>
    </div>
@endforeach
                </div>
            </div>
        </div>
    </div>
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
        --accent-red: #ef4444;
        --text-primary: #ffffff;
        --text-secondary: #b0b7c3;
        --glass-bg: rgba(255,255,255,0.05);
        --glass-border: rgba(255,255,255,0.1);
    }

    body, html, .wrapper, .content-wrapper {
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

    .glass-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: 0.3s;
        padding: 1.5rem;
    }

    .filters-section {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        color: var(--text-primary);
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .summary-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .summary-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255,255,255,0.2);
    }

    .summary-card.presidencial::before {
        background: linear-gradient(90deg, #ffd700, #ff8c00);
    }

    .summary-card.diputados::before {
        background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
    }

    .summary-card.alcaldes::before {
        background: linear-gradient(90deg, var(--accent-green), #06d6a0);
    }

    .summary-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: var(--accent-blue);
    }

    .summary-number {
        font-size: 2.5rem;
        font-weight: 800;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .summary-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .time-selector {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .time-btn {
        padding: 0.5rem 1.5rem;
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: 25px;
        color: var(--text-primary);
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        border: none;
    }

    .time-btn:hover,
    .time-btn.active {
        background: var(--primary-gradient);
        border-color: transparent;
        transform: translateY(-2px);
    }

    .reports-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .timeline-container {
        border-radius: 20px;
        padding: 2rem;
    }

    .timeline-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .timeline-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .timeline-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .stats-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-card {
        border-radius: 20px;
        padding: 1.5rem;
    }

    .stat-card-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .stat-card-header i {
        font-size: 1.2rem;
        color: var(--accent-blue);
    }

    .stat-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .progress-item {
        margin-bottom: 1rem;
    }

    .progress-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .progress-label {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .progress-value {
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .progress-bar.bg-primary {
        background: var(--primary-gradient) !important;
    }

    .department-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--glass-border);
    }

    .department-item:last-child {
        border-bottom: none;
    }

    .department-name {
        color: var(--text-primary);
        font-weight: 500;
    }

    .department-count {
        background: var(--accent-blue);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Responsive */

    @media (max-width: 1024px) {
        .reports-grid {
            grid-template-columns: 1fr;
        }
        .stats-container {
            grid-row: 1;
        }
    }

    @media (max-width: 768px) {
        .time-selector {
            justify-content: center;
        }
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const evolucionData = @json($evolucionRegistros);
        const timeButtons = document.querySelectorAll('.time-btn');

        let timelineChart;

        // Initialize chart
        initializeChart(evolucionData);

        // Time period buttons
        timeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                timeButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const period = this.dataset.period;
                updateChart(period);
            });
        });

        function initializeChart(data) {
            const ctx = document.getElementById('timelineChart').getContext('2d');

            timelineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => {
                        const date = new Date(item.fecha);
                        return date.toLocaleDateString('es-ES', {
                            month: 'short',
                            day: 'numeric'
                        });
                    }),
                    datasets: [{
                        label: 'Candidatos Registrados',
                        data: data.map(item => item.total),
                        borderColor: '#00d4ff',
                        backgroundColor: 'rgba(0, 212, 255, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#00d4ff',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#ffffff',
                                font: {
                                    size: 14,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: '#00d4ff',
                            borderWidth: 1,
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                                borderColor: 'rgba(255, 255, 255, 0.2)'
                            },
                            ticks: {
                                color: '#b0b7c3',
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)',
                                borderColor: 'rgba(255, 255, 255, 0.2)'
                            },
                            ticks: {
                                color: '#b0b7c3',
                                font: {
                                    size: 12
                                },
                                beginAtZero: true
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: '#ffffff'
                        }
                    }
                }
            });
        }

        function updateChart(period) {
            // Show loading
            const chartWrapper = document.querySelector('.chart-wrapper');
            chartWrapper.innerHTML = `
                <div class="loading d-flex align-items-center justify-content-center" style="height: 200px; color: #b0b7c3;">
                    <div class="spinner" style="width:40px; height:40px; border: 4px solid rgba(255,255,255,0.15); border-top: 4px solid #00d4ff; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 1rem;"></div>
                    <span>Cargando datos...</span>
                </div>
            `;

            // Simulate API call delay
            setTimeout(() => {
                // Recreate canvas
                chartWrapper.innerHTML = '<canvas id="timelineChart"></canvas>';

                // Filter data based on period
                let filteredData = evolucionData;

                if (period !== 'all') {
                    const days = parseInt(period);
                    const cutoffDate = new Date();
                    cutoffDate.setDate(cutoffDate.getDate() - days);

                    filteredData = evolucionData.filter(item => {
                        const itemDate = new Date(item.fecha);
                        return itemDate >= cutoffDate;
                    });
                }

                // Reinitialize chart with filtered data
                initializeChart(filteredData);
            }, 500);
        }
    });
</script>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@stop
