<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa Electoral - Honduras</title>

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
            --accent-red: #ef4444;
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
            max-width: 1600px;
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

        /* Stats Bar */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Main Content */
        .main-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Map Container */
        .map-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            position: relative;
            min-height: 600px;
        }

        .map-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .map-header h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .map-subtitle {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* SVG Map Styles */
        .honduras-map {
            width: 100%;
            height: 500px;
            border-radius: 15px;
        }

        .department {
            fill: var(--glass-bg);
            stroke: var(--glass-border);
            stroke-width: 2;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .department:hover {
            stroke: var(--accent-blue);
            stroke-width: 3;
            filter: brightness(1.2);
        }

        .department.selected {
            stroke: var(--accent-yellow);
            stroke-width: 4;
        }

        /* Heat Map Colors */
        .heat-1 { fill: rgba(16, 185, 129, 0.3); }
        .heat-2 { fill: rgba(16, 185, 129, 0.5); }
        .heat-3 { fill: rgba(245, 158, 11, 0.5); }
        .heat-4 { fill: rgba(239, 68, 68, 0.5); }
        .heat-5 { fill: rgba(239, 68, 68, 0.8); }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .info-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
        }

        .panel-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .department-info h4 {
            font-size: 1.1rem;
            color: var(--accent-blue);
            margin-bottom: 0.5rem;
        }

        .candidate-type {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .candidate-type:last-child {
            border-bottom: none;
        }

        .type-label {
            color: var(--text-secondary);
        }

        .type-count {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Legend */
        .legend {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1rem;
        }

        .legend-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.3rem;
            font-size: 0.8rem;
        }

        .legend-color {
            width: 20px;
            height: 15px;
            border-radius: 3px;
        }

        /* Filters */
        .map-filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 25px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-gradient);
            border-color: transparent;
        }

        /* Tooltip */
        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.8rem;
            pointer-events: none;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tooltip.show {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .sidebar {
                grid-row: 1;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .stats-bar {
                grid-template-columns: repeat(2, 1fr);
            }

            .page-header {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-map-marked-alt"></i> Mapa Electoral de Honduras
            </h1>
            <a href="{{ route('dashboard.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Volver al Dashboard</span>
            </a>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number">{{ $candidatosPorDepartamento->flatten()->sum() }}</div>
                <div class="stat-label">Total Candidatos</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $candidatosPorDepartamento->count() }}</div>
                <div class="stat-label">Departamentos</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $candidatosPorDepartamento->flatten()->where('tipo_candidato', 'presidencial')->sum('total') }}</div>
                <div class="stat-label">Presidenciales</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $candidatosPorDepartamento->flatten()->where('tipo_candidato', 'diputado')->sum('total') }}</div>
                <div class="stat-label">Diputados</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $candidatosPorDepartamento->flatten()->where('tipo_candidato', 'alcalde')->sum('total') }}</div>
                <div class="stat-label">Alcaldes</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="map-filters">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-globe"></i> Todos
            </button>
            <button class="filter-btn" data-filter="presidencial">
                <i class="fas fa-crown"></i> Presidenciales
            </button>
            <button class="filter-btn" data-filter="diputado">
                <i class="fas fa-users"></i> Diputados
            </button>
            <button class="filter-btn" data-filter="alcalde">
                <i class="fas fa-building"></i> Alcaldes
            </button>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Map Container -->
            <div class="map-container">
                <div class="map-header">
                    <h3>Distribución de Candidatos por Departamento</h3>
                    <p class="map-subtitle">Haz clic en un departamento para ver detalles</p>
                </div>

                <!-- SVG Map of Honduras -->
                <svg class="honduras-map" viewBox="0 0 800 400" xmlns="http://www.w3.org/2000/svg">
                    <!-- Simplified Honduras Map -->

                    <!-- Atlántida -->
                    <path class="department" data-department="ATLANTIDA" data-name="Atlántida"
                          d="M 200 120 L 280 110 L 290 140 L 250 150 L 200 140 Z" />

                    <!-- Colón -->
                    <path class="department" data-department="COLON" data-name="Colón"
                          d="M 280 110 L 350 100 L 360 130 L 290 140 Z" />

                    <!-- Cortés -->
                    <path class="department" data-department="CORTES" data-name="Cortés"
                          d="M 150 140 L 200 120 L 200 140 L 180 160 L 150 160 Z" />

                    <!-- Francisco Morazán -->
                    <path class="department" data-department="FRANCISCO MORAZAN" data-name="Francisco Morazán"
                          d="M 300 200 L 380 190 L 390 230 L 320 240 L 300 220 Z" />

                    <!-- Comayagua -->
                    <path class="department" data-department="COMAYAGUA" data-name="Comayagua"
                          d="M 250 180 L 300 170 L 300 200 L 270 210 L 250 200 Z" />

                    <!-- Yoro -->
                    <path class="department" data-department="YORO" data-name="Yoro"
                          d="M 200 160 L 250 150 L 250 180 L 220 190 L 200 180 Z" />

                    <!-- Santa Bárbara -->
                    <path class="department" data-department="SANTA BARBARA" data-name="Santa Bárbara"
                          d="M 150 160 L 200 160 L 200 180 L 170 190 L 150 180 Z" />

                    <!-- Copán -->
                    <path class="department" data-department="COPAN" data-name="Copán"
                          d="M 100 180 L 150 180 L 150 200 L 120 210 L 100 200 Z" />

                    <!-- Ocotepeque -->
                    <path class="department" data-department="OCOTEPEQUE" data-name="Ocotepeque"
                          d="M 80 200 L 120 210 L 120 230 L 90 240 L 80 220 Z" />

                    <!-- Lempira -->
                    <path class="department" data-department="LEMPIRA" data-name="Lempira"
                          d="M 120 230 L 170 220 L 170 250 L 140 260 L 120 250 Z" />

                    <!-- Intibucá -->
                    <path class="department" data-department="INTIBUCA" data-name="Intibucá"
                          d="M 170 250 L 220 240 L 220 270 L 190 280 L 170 270 Z" />

                    <!-- La Paz -->
                    <path class="department" data-department="LA PAZ" data-name="La Paz"
                          d="M 220 270 L 270 260 L 270 290 L 240 300 L 220 290 Z" />

                    <!-- Valle -->
                    <path class="department" data-department="VALLE" data-name="Valle"
                          d="M 240 300 L 290 290 L 290 320 L 260 330 L 240 320 Z" />

                    <!-- Choluteca -->
                    <path class="department" data-department="CHOLUTECA" data-name="Choluteca"
                          d="M 290 290 L 340 280 L 340 310 L 310 320 L 290 310 Z" />

                    <!-- El Paraíso -->
                    <path class="department" data-department="EL PARAISO" data-name="El Paraíso"
                          d="M 390 230 L 450 220 L 450 260 L 420 270 L 390 250 Z" />

                    <!-- Olancho -->
                    <path class="department" data-department="OLANCHO" data-name="Olancho"
                          d="M 380 190 L 450 180 L 450 220 L 420 230 L 380 210 Z" />

                    <!-- Gracias a Dios -->
                    <path class="department" data-department="GRACIAS A DIOS" data-name="Gracias a Dios"
                          d="M 450 120 L 550 110 L 550 180 L 480 190 L 450 170 Z" />

                    <!-- Islas de la Bahía -->
                    <path class="department" data-department="ISLAS DE LA BAHIA" data-name="Islas de la Bahía"
                          d="M 250 80 L 300 75 L 305 95 L 255 100 Z" />
                </svg>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Department Info -->
                <div class="info-panel">
                    <div class="panel-title">
                        <i class="fas fa-info-circle"></i>
                        Información del Departamento
                    </div>
                    <div id="department-info">
                        <p style="color: var(--text-secondary); text-align: center; padding: 2rem;">
                            Selecciona un departamento en el mapa para ver la información
                        </p>
                    </div>
                </div>

                <!-- Legend -->
                <div class="legend">
                    <div class="legend-title">Leyenda - Intensidad de Candidatos</div>
                    <div class="legend-item">
                        <div class="legend-color heat-1"></div>
                        <span>1-5 candidatos</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color heat-2"></div>
                        <span>6-15 candidatos</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color heat-3"></div>
                        <span>16-30 candidatos</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color heat-4"></div>
                        <span>31-50 candidatos</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color heat-5"></div>
                        <span>50+ candidatos</span>
                    </div>
                </div>

                <!-- Top Departments -->
                <div class="info-panel">
                    <div class="panel-title">
                        <i class="fas fa-trophy"></i>
                        Top Departamentos
                    </div>
                    <div id="top-departments">
                        @foreach($candidatosPorDepartamento->sortByDesc(function($dept) { return $dept->sum('total'); })->take(5) as $departamento => $candidatos)
                        <div class="candidate-type">
                            <span class="type-label">{{ $departamento }}</span>
                            <span class="type-count">{{ $candidatos->sum('total') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tooltip -->
    <div class="tooltip" id="tooltip"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const candidatosPorDepartamento = @json($candidatosPorDepartamento);
            const departments = document.querySelectorAll('.department');
            const tooltip = document.getElementById('tooltip');
            const departmentInfo = document.getElementById('department-info');
            const filterBtns = document.querySelectorAll('.filter-btn');

            let currentFilter = 'all';

            // Initialize map colors
            updateMapColors();

            // Department click handlers
            departments.forEach(dept => {
                const deptName = dept.dataset.name;
                const deptKey = dept.dataset.department;

                dept.addEventListener('click', function() {
                    // Remove previous selection
                    departments.forEach(d => d.classList.remove('selected'));
                    // Add selection to current
                    this.classList.add('selected');

                    // Update info panel
                    updateDepartmentInfo(deptName, candidatosPorDepartamento[deptName] || []);
                });

                // Tooltip on hover
                dept.addEventListener('mouseenter', function(e) {
                    const data = candidatosPorDepartamento[deptName] || [];
                    const total = data.reduce((sum, item) => sum + (item.total || 0), 0);

                    tooltip.innerHTML = `
                        <strong>${deptName}</strong><br>
                        Total: ${total} candidatos
                    `;
                    tooltip.classList.add('show');
                });

                dept.addEventListener('mousemove', function(e) {
                    tooltip.style.left = e.pageX + 10 + 'px';
                    tooltip.style.top = e.pageY - 30 + 'px';
                });

                dept.addEventListener('mouseleave', function() {
                    tooltip.classList.remove('show');
                });
            });

            // Filter buttons
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.dataset.filter;
                    updateMapColors();
                });
            });

            function updateMapColors() {
                departments.forEach(dept => {
                    const deptName = dept.dataset.name;
                    const data = candidatosPorDepartamento[deptName] || [];

                    let total = 0;
                    if (currentFilter === 'all') {
                        total = data.reduce((sum, item) => sum + (item.total || 0), 0);
                    } else {
                        const filtered = data.find(item => item.tipo_candidato === currentFilter);
                        total = filtered ? filtered.total : 0;
                    }

                    // Remove existing heat classes
                    dept.classList.remove('heat-1', 'heat-2', 'heat-3', 'heat-4', 'heat-5');

                    // Add appropriate heat class
                    if (total > 50) dept.classList.add('heat-5');
                    else if (total > 30) dept.classList.add('heat-4');
                    else if (total > 15) dept.classList.add('heat-3');
                    else if (total > 5) dept.classList.add('heat-2');
                    else if (total > 0) dept.classList.add('heat-1');
                });
            }

            function updateDepartmentInfo(deptName, data) {
                const presidenciales = data.find(item => item.tipo_candidato === 'presidencial')?.total || 0;
                const diputados = data.find(item => item.tipo_candidato === 'diputado')?.total || 0;
                const alcaldes = data.find(item => item.tipo_candidato === 'alcalde')?.total || 0;
                const total = presidenciales + diputados + alcaldes;

                departmentInfo.innerHTML = `
                    <div class="department-info">
                        <h4>${deptName}</h4>
                        <div class="candidate-type">
                            <span class="type-label">Total Candidatos</span>
                            <span class="type-count">${total}</span>
                        </div>
                        <div class="candidate-type">
                            <span class="type-label">Presidenciales</span>
                            <span class="type-count">${presidenciales}</span>
                        </div>
                        <div class="candidate-type">
                            <span class="type-label">Diputados</span>
                            <span class="type-count">${diputados}</span>
                        </div>
                        <div class="candidate-type">
                            <span class="type-label">Alcaldes</span>
                            <span class="type-count">${alcaldes}</span>
                        </div>
                    </div>
                `;
            }
        });
    </script>
</body>
</html>
