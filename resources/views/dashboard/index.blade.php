{{-- resources/views/home.blade.php --}}
@extends('adminlte::page')

@section('title', 'Dashboard Electoral')

{{-- --- CSS y fuentes --- --}}
@push('css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* --- variables y resets --- */
        :root{--primary-gradient:linear-gradient(135deg,#667eea 0%,#764ba2 100%);--secondary-gradient:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);--dark-gradient:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);--accent-blue:#00d4ff;--accent-purple:#8b5cf6;--accent-pink:#ec4899;--accent-green:#10b981;--accent-yellow:#f59e0b;--text-primary:#ffffff;--text-secondary:#b0b7c3;--glass-bg:rgba(255,255,255,.05);--glass-border:rgba(255,255,255,.1)}
        *{margin:0;padding:0;box-sizing:border-box}body{font-family:'Inter',sans-serif;background:var(--dark-gradient);color:var(--text-primary);overflow-x:hidden}
        body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:radial-gradient(circle at 20% 80%,rgba(120,119,198,.3)0%,transparent 50%),radial-gradient(circle at 80% 20%,rgba(255,119,198,.3)0%,transparent 50%);pointer-events:none;z-index:-1}

        /* --- layout --- */
        .dashboard-container{max-width:1400px;margin:0 auto;padding:2rem;min-height:100vh}
        .dashboard-header{text-align:center;margin-bottom:3rem}
        .dashboard-title{font-size:3rem;font-weight:800;background:var(--primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:1rem}
        .dashboard-subtitle{color:var(--text-secondary);font-size:1.2rem}

        /* --- stats --- */
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem;margin-bottom:3rem}
        .stat-card{background:var(--glass-bg);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:20px;padding:2rem;position:relative;overflow:hidden;transition:.3s}
        .stat-card::before{content:'';position:absolute;top:0;left:0;width:100%;height:3px;background:var(--primary-gradient)}
        .stat-card.presidencial::before{background:linear-gradient(90deg,#ffd700,#ff8c00)}
        .stat-card.diputados::before{background:linear-gradient(90deg,var(--accent-blue),var(--accent-purple))}
        .stat-card.alcaldes::before{background:linear-gradient(90deg,var(--accent-green),#06d6a0)}
        .stat-card.total::before{background:var(--secondary-gradient)}
        .stat-card:hover{transform:translateY(-10px);border-color:rgba(255,255,255,.2);box-shadow:0 20px 40px rgba(0,0,0,.3)}
        .stat-icon{display:inline-flex;align-items:center;justify-content:center;width:60px;height:60px;background:var(--primary-gradient);border-radius:15px;margin-bottom:1rem}
        .stat-icon i{font-size:1.5rem;color:#fff}
        .stat-content h3{font-size:2.5rem;font-weight:800;margin-bottom:.5rem;background:var(--primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .stat-content p{color:var(--text-secondary);font-size:.9rem}

        /* --- charts; acciones rápidas; carrusel; responsivo --- */
        .charts-section{display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:2rem;margin-bottom:3rem}
        .chart-container{background:var(--glass-bg);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:20px;padding:2rem}
        .chart-header{text-align:center;margin-bottom:1.5rem}.chart-header h3{font-size:1.2rem;font-weight:600;color:var(--text-primary)}
        .quick-actions{display:flex;gap:1rem;margin-bottom:3rem;justify-content:center;flex-wrap:wrap}
        .action-btn{display:flex;align-items:center;gap:.5rem;padding:1rem 2rem;background:var(--glass-bg);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:50px;color:var(--text-primary);text-decoration:none;transition:.3s;position:relative;overflow:hidden}
        .action-btn::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:var(--primary-gradient);transition:left .3s;z-index:-1}
        .action-btn:hover::before{left:0}.action-btn:hover{transform:translateY(-3px);box-shadow:0 10px 25px rgba(0,0,0,.3)}
        .carousel-section{background:var(--glass-bg);backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:25px;padding:2rem}
        .carousel-header{text-align:center;margin-bottom:2rem}.carousel-header h3{font-size:2rem;font-weight:700;background:var(--primary-gradient);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .candidates-carousel{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:2rem;max-height:600px;overflow-y:auto;padding-right:1rem}
        .candidates-carousel::-webkit-scrollbar{width:8px}.candidates-carousel::-webkit-scrollbar-track{background:rgba(255,255,255,.1);border-radius:10px}.candidates-carousel::-webkit-scrollbar-thumb{background:var(--primary-gradient);border-radius:10px}
        .candidate-card{perspective:1000px;height:350px;cursor:pointer}.candidate-front,.candidate-back{position:absolute;width:100%;height:100%;backface-visibility:hidden;border-radius:20px;transition:transform .6s;background:var(--glass-bg);backdrop-filter:blur(20px);border:1px solid var(--glass-border)}
        .candidate-back{transform:rotateY(180deg)}.candidate-card.flipped .candidate-front{transform:rotateY(180deg)}.candidate-card.flipped .candidate-back{transform:rotateY(0)}
        .candidate-front{display:flex;flex-direction:column;align-items:center;padding:2rem;text-align:center}.candidate-photo{width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid var(--accent-blue);margin-bottom:1rem;box-shadow:0 10px 30px rgba(0,212,255,.3)}
        .candidate-info h4{font-size:1.3rem;font-weight:700;margin-bottom:.5rem}.candidate-type{background:var(--primary-gradient);color:#fff;padding:.3rem 1rem;border-radius:20px;font-size:.8rem;font-weight:600;margin-bottom:.5rem;display:inline-block}
        .candidate-party{color:var(--text-secondary);font-size:.9rem}.candidate-back{padding:2rem;display:flex;flex-direction:column;justify-content:space-between}
        .candidate-details h4{text-align:center;font-size:1.2rem;font-weight:700;margin-bottom:1rem}.candidate-proposals h5{color:var(--accent-blue);margin-bottom:.5rem;font-size:1rem}.candidate-proposals p{color:var(--text-secondary);line-height:1.6;font-size:.9rem}
        .candidate-metadata{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:1rem}.candidate-metadata span{padding:.3rem .8rem;border-radius:15px;font-size:.8rem;font-weight:600}
        .department{background:rgba(139,92,246,.2);color:var(--accent-purple);border:1px solid var(--accent-purple)}.reelection{background:rgba(245,158,11,.2);color:var(--accent-yellow);border:1px solid var(--accent-yellow)}
        .empty-state{text-align:center;padding:4rem 2rem;color:var(--text-secondary)}.empty-icon{font-size:4rem;margin-bottom:1rem;opacity:.5}.empty-state h3{font-size:1.5rem;margin-bottom:.5rem;color:var(--text-primary)}
        @media(max-width:768px){.dashboard-container{padding:1rem}.stats-grid{grid-template-columns:1fr}.charts-section{grid-template-columns:1fr}.quick-actions{flex-direction:column;align-items:center}.candidates-carousel{grid-template-columns:1fr}.dashboard-title{font-size:2rem}}
    </style>
    
    <style>
        /* --- Fondo oscuro para el área de contenido central --- */
        .content-wrapper,
        .content,
        .dashboard-container {
            /* Usa tu gradiente o un gris sólido */
            background: var(--dark-gradient);   /* ← mismo gradiente del <body> */
            /* Si prefieres un tono fijo:  background:#1f2937; */
        }

        /* Opcional: da un poco más de contraste a las cards sobre el nuevo fondo */
        .stat-card,
        .chart-container,
        .carousel-section {
            background: rgba(255,255,255,0.04);       /* mayor transparencia */
        }
    </style>

@endpush

{{-- --- JAVASCRIPT (Chart.js + lógica) --- --}}
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const estadisticas   = @json($estadisticas   ?? []);
            const datosGraficos  = @json($datosGraficos  ?? []);

/* ====== Género ====== */
const generoData = datosGraficos.candidatos_por_genero ?? [];
const labelsGenero = generoData.map(g => g.genero);
const datosGenero = generoData.map(g => g.total);

new Chart(document.getElementById('generoChart'), {
    type:'doughnut',
    data: {
        labels: labelsGenero,
        datasets: [{
            data: datosGenero,
            backgroundColor: ['#4F46E5', '#EC4899', '#F59E0B', '#10B981'], // colores adicionales si hay más géneros
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#fff' }
            }
        }
    }
});

            /* ====== Completitud ====== */
            new Chart(document.getElementById('completitudChart'), {
                type:'bar',
                data:{labels:['Completos','Incompletos'],
                      datasets:[{data:[estadisticas.perfiles_completos ?? 0, estadisticas.perfiles_incompletos ?? 0],
                                 backgroundColor:['#10B981','#F59E0B'],borderWidth:0}]},
                options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{ticks:{color:'#fff'}},x:{ticks:{color:'#fff'}}}}
            });

            /* ====== Departamentos ====== */
            const depData = datosGraficos.candidatos_por_departamento ?? [];
            new Chart(document.getElementById('departamentoChart'), {
                type:'line',
                data:{labels:depData.map(d => d.departamento || 'Sin datos'),
                      datasets:[{label:'Candidatos',
                                 data:depData.map(d => d.total || 0),
                                 borderColor:'#8B5CF6',backgroundColor:'rgba(139,92,246,.1)',
                                 borderWidth:2,fill:true}]},
                options:{responsive:true,plugins:{legend:{labels:{color:'#fff'}}},
                         scales:{y:{ticks:{color:'#fff'}},x:{ticks:{color:'#fff'}}}}
            });

            /* ====== Flip cards ====== */
            document.querySelectorAll('.candidate-card').forEach(card =>
                card.addEventListener('click', () => card.classList.toggle('flipped'))
            );

            /* ====== Actualizar stats en vivo cada 30 seg ====== */
            setInterval(() => {
                fetch('{{ route("dashboard.api.stats") }}')
                    .then(r => r.json())
                    .then(data => console.log('Stats updated', data))
                    .catch(err => console.error(err));
            }, 30000);
        });
    </script>
@endpush

{{-- --- Contenido principal (solo lo que iba dentro del <body>) --- --}}
@section('content')
    <div class="dashboard-container">
        {{-- Header --}}
        <div class="dashboard-header">
            <h1 class="dashboard-title"><i class="fas fa-vote-yea"></i> Dashboard Electoral</h1>
            <p class="dashboard-subtitle">Sistema de Gestión Electoral – TuVoto Cipote</p>
        </div>

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card presidencial">
                <div class="stat-icon"><i class="fas fa-crown"></i></div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['candidatos_presidenciales'] ?? 0 }}</h3>
                    <p>Candidatos Presidenciales</p>
                </div>
            </div>

            <div class="stat-card diputados">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['candidatos_diputados'] ?? 0 }}</h3>
                    <p>Candidatos a Diputados</p>
                </div>
            </div>

            <div class="stat-card alcaldes">
                <div class="stat-icon"><i class="fas fa-building"></i></div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['candidatos_alcaldes'] ?? 0 }}</h3>
                    <p>Candidatos a Alcaldes</p>
                </div>
            </div>

            <div class="stat-card total">
                <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-content">
                    <h3>{{ $estadisticas['total_candidatos'] ?? 0 }}</h3>
                    <p>Total de Candidatos</p>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="charts-section">
            <div class="chart-container">
                <div class="chart-header"><h3>Distribución por Género</h3></div>
                <canvas id="generoChart"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-header"><h3>Completitud de Perfiles</h3></div>
                <canvas id="completitudChart"></canvas>
            </div>
            <div class="chart-container">
                <div class="chart-header"><h3>Candidatos por Departamento</h3></div>
                <canvas id="departamentoChart"></canvas>
            </div>
        </div>
    </div>
@stop