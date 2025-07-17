<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Electoral - TuVoto Cipote</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --dark-gradient: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            --accent-blue: #00d4ff;
            --accent-purple: #8b5cf6;
            --accent-pink: #ec4899;
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

        /* Layout Container */
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
        }

        /* Header */
        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .dashboard-title {
            font-size: 3rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }

        .dashboard-subtitle {
            color: var(--text-secondary);
            font-size: 1.2rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-gradient);
        }

        .stat-card.presidencial::before {
            background: linear-gradient(90deg, #ffd700, #ff8c00);
        }

        .stat-card.diputados::before {
            background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
        }

        .stat-card.alcaldes::before {
            background: linear-gradient(90deg, var(--accent-green), #06d6a0);
        }

        .stat-card.total::before {
            background: var(--secondary-gradient);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            border-color: rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: var(--primary-gradient);
            border-radius: 15px;
            margin-bottom: 1rem;
        }

        .stat-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .stat-content h3 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-content p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .chart-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
        }

        .chart-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .chart-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 50px;
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--primary-gradient);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .action-btn:hover::before {
            left: 0;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
            text-decoration: none;
        }

        /* Carousel Section */
        .carousel-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .carousel-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .carousel-header h3 {
            font-size: 2rem;
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .candidates-carousel {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            max-height: 600px;
            overflow-y: auto;
            padding-right: 1rem;
        }

        /* Custom Scrollbar */
        .candidates-carousel::-webkit-scrollbar {
            width: 8px;
        }

        .candidates-carousel::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .candidates-carousel::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        /* Candidate Cards */
        .candidate-card {
            perspective: 1000px;
            height: 350px;
            cursor: pointer;
        }

        .candidate-front,
        .candidate-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 20px;
            transition: transform 0.6s ease;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
        }

        .candidate-back {
            transform: rotateY(180deg);
        }

        .candidate-card.flipped .candidate-front {
            transform: rotateY(180deg);
        }

        .candidate-card.flipped .candidate-back {
            transform: rotateY(0deg);
        }

        .candidate-front {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            text-align: center;
        }

        .candidate-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--accent-blue);
            margin-bottom: 1rem;
            box-shadow: 0 10px 30px rgba(0, 212, 255, 0.3);
        }

        .candidate-info h4 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .candidate-type {
            background: var(--primary-gradient);
            color: white;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .candidate-party {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Candidate Back */
        .candidate-back {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .candidate-details h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: center;
            color: var(--text-primary);
        }

        .candidate-proposals h5 {
            color: var(--accent-blue);
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .candidate-proposals p {
            color: var(--text-secondary);
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .candidate-metadata {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }

        .candidate-metadata span {
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .department {
            background: rgba(139, 92, 246, 0.2);
            color: var(--accent-purple);
            border: 1px solid var(--accent-purple);
        }

        .reelection {
            background: rgba(245, 158, 11, 0.2);
            color: var(--accent-yellow);
            border: 1px solid var(--accent-yellow);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .charts-section {
                grid-template-columns: 1fr;
            }

            .quick-actions {
                flex-direction: column;
                align-items: center;
            }

            .candidates-carousel {
                grid-template-columns: 1fr;
            }

            .dashboard-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-vote-yea"></i> Dashboard Electoral
            </h1>
            <p class="dashboard-subtitle">Sistema de Gestión Electoral - TuVoto Cipote</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card presidencial">
                <div class="stat-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['candidatos_presidenciales'] ?? 0 }}</h3>
                    <p>Candidatos Presidenciales</p>
                </div>
            </div>

            <div class="stat-card diputados">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['candidatos_diputados'] ?? 0 }}</h3>
                    <p>Candidatos a Diputados</p>
                </div>
            </div>

            <div class="stat-card alcaldes">
                <div class="stat-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['candidatos_alcaldes'] ?? 0 }}</h3>
                    <p>Candidatos a Alcaldes</p>
                </div>
            </div>

            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['total_candidatos'] ?? 0 }}</h3>
                    <p>Total de Candidatos</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Distribución por Género</h3>
                </div>
                <canvas id="generoChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h3>Completitud de Perfiles</h3>
                </div>
                <canvas id="completitudChart"></canvas>
            </div>

            <div class="chart-container">
                <div class="chart-header">
                    <h3>Candidatos por Departamento</h3>
                </div>
                <canvas id="departamentoChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <a href="{{ route('dashboard.candidatos') }}" class="action-btn">
                <i class="fas fa-users"></i>
                <span>Ver Candidatos</span>
            </a>
            <a href="{{ route('candidates.index') }}" class="action-btn">
                <i class="fas fa-cog"></i>
                <span>Gestión</span>
            </a>
            <a href="{{ route('home') }}" class="action-btn">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </div>

        <!-- Candidates Carousel -->
        <div class="carousel-section">
            <div class="carousel-header">
                <h3>Candidatos Destacados</h3>
            </div>
            <div class="candidates-carousel">
                @forelse($candidatosCarrusel as $candidato)
                <div class="candidate-card">
                    <div class="candidate-front">
                        <img src="{{ $candidato->fotografia_url }}" alt="{{ $candidato->nombre_completo }}" class="candidate-photo">
                        <div class="candidate-info">
                            <h4>{{ $candidato->nombre_completo }}</h4>
                            <p class="candidate-type">{{ ucfirst($candidato->tipo_candidato ?? 'Candidato') }}</p>
                            <p class="candidate-party">{{ $candidato->party->name ?? 'Independiente' }}</p>
                        </div>
                    </div>
                    <div class="candidate-back">
                        <div class="candidate-details">
                            <h4>{{ $candidato->nombre_completo }}</h4>
                            <div class="candidate-proposals">
                                <h5>Propuestas:</h5>
                                <p>{{ Str::limit($candidato->propuestas, 150) ?: 'Sin propuestas registradas' }}</p>
                            </div>
                            <div class="candidate-metadata">
                                @if($candidato->departamento)
                                    <span class="department">{{ $candidato->departamento->name }}</span>
                                @endif
                                @if($candidato->reeleccion)
                                    <span class="reelection">Reelección</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h3>No hay candidatos destacados</h3>
                    <p>Aún no hay candidatos con perfiles completos para mostrar.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Datos para gráficos
            const datosGraficos = @json($datosGraficos ?? []);
            const estadisticas = @json($estadisticas ?? []);

            // Gráfico de Género
            const generoCtx = document.getElementById('generoChart').getContext('2d');
            new Chart(generoCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hombres', 'Mujeres'],
                    datasets: [{
                        data: [estadisticas.hombres || 0, estadisticas.mujeres || 0],
                        backgroundColor: ['#4F46E5', '#EC4899'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#fff'
                            }
                        }
                    }
                }
            });

            // Gráfico de Completitud
            const completitudCtx = document.getElementById('completitudChart').getContext('2d');
            new Chart(completitudCtx, {
                type: 'bar',
                data: {
                    labels: ['Completos', 'Incompletos'],
                    datasets: [{
                        data: [estadisticas.perfiles_completos || 0, estadisticas.perfiles_incompletos || 0],
                        backgroundColor: ['#10B981', '#F59E0B'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: '#fff'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff'
                            }
                        }
                    }
                }
            });

            // Gráfico de Departamentos
            const departamentoCtx = document.getElementById('departamentoChart').getContext('2d');
            const departamentosData = datosGraficos.candidatos_por_departamento || [];

            new Chart(departamentoCtx, {
                type: 'line',
                data: {
                    labels: departamentosData.map(item => item.departamento || 'Sin datos'),
                    datasets: [{
                        label: 'Candidatos',
                        data: departamentosData.map(item => item.total || 0),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#fff'
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                color: '#fff'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#fff'
                            }
                        }
                    }
                }
            });

            // Card flip animation
            document.querySelectorAll('.candidate-card').forEach(card => {
                card.addEventListener('click', function() {
                    this.classList.toggle('flipped');
                });
            });

            // Auto-update stats every 30 seconds
            setInterval(() => {
                fetch('{{ route("dashboard.api.stats") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Update stat cards with new data
                        console.log('Stats updated:', data);
                    })
                    .catch(error => console.error('Error updating stats:', error));
            }, 30000);
        });
    </script>
</body>
</html>

