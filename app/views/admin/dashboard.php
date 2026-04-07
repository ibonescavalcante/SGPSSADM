<?php $this->layout('admin_template') ?>

<div class="dashboard-header">
    <div class="welcome-section">
        <h1 class="admin-title">Dashboard Administrativo</h1>
        <p class="admin-subtitle">Bem-vindo ao painel de controle do sistema PSS</p>
    </div>
    <div class="dashboard-actions">
        <a href="/admin/pss/criar-novo" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Novo PSS
        </a>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="stats-grid">
    <div class="stat-card stat-primary">
        <div class="stat-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-number"><?= $total_pss ?? 0 ?></h3>
            <p class="stat-label">PSS Ativos</p>
            <div class="stat-trend">
                <i class="fas fa-arrow-up"></i>
                <span>+12% este mês</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-success">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-number"><?= $total_inscricoes ?? 0 ?></h3>
            <p class="stat-label">Inscrições</p>
            <div class="stat-trend">
                <i class="fas fa-arrow-up"></i>
                <span>+8% este mês</span>
            </div>
        </div>
    </div>

    <div class="stat-card stat-info">
        <div class="stat-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <h3 class="stat-number"><?= $recursos_pendentes ?? 0 ?></h3>
            <p class="stat-label">Recursos</p>
            <div class="stat-trend">
                <i class="fas fa-minus"></i>
                <span>Estável</span>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- PSS Recentes -->
    <div class="card pss-recentes-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-file-alt"></i>
                PSS Recentes
            </h3>
            <a href="/admin/pss" class="btn btn-sm btn-outline">Ver Todos</a>
        </div>
        <div class="card-body">
            <?php if (!empty($pss_recentes)): ?>
                <div class="pss-list">
                    <?php foreach ($pss_recentes as $pss): ?>
                        <div class="pss-item">
                            <div class="pss-header">
                                <div class="pss-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="pss-info">
                                    <h4 class="pss-title"><?= htmlspecialchars($pss['titulo'] ?? 'PSS sem título') ?></h4>
                                    <p class="pss-subtitle">
                                        <?= htmlspecialchars($pss['secretaria'] ?? 'Secretaria não informada') ?>
                                        <?php if (!empty($pss['ano_exercicio'])): ?>
                                            - <?= htmlspecialchars($pss['ano_exercicio']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="pss-status">
                                    <?php 
                                    $status = $pss['status_global'] ?? 'indefinido';
                                    $badge_class = $status === 'em_andamento' ? 'success' : 'secondary';
                                    $status_texto = $status === 'em_andamento' ? 'Em Andamento' : ucfirst(str_replace('_', ' ', $status));
                                    ?>
                                    <span class="status-badge status-<?= $badge_class ?>">
                                        <i class="fas fa-circle"></i>
                                        <?= htmlspecialchars($status_texto) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="pss-details">
                                <div class="pss-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        Criado em: 
                                        <?php 
                                        $data_criacao = $pss['criado_em'] ?? null;
                                        if ($data_criacao) {
                                            echo date('d/m/Y', strtotime($data_criacao));
                                        } else {
                                            echo 'Data não informada';
                                        }
                                        ?>
                                    </span>
                                    <?php if (!empty($pss['inscricao_ini']) && !empty($pss['inscricao_fim'])): ?>
                                        <span class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            Inscrições: <?= date('d/m', strtotime($pss['inscricao_ini'])) ?> a <?= date('d/m/Y', strtotime($pss['inscricao_fim'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="pss-actions">
                                    <a href="/admin/pss/<?= $pss['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i>
                                        Visualizar
                                    </a>
                                    <a href="/admin/pss/<?= $pss['id'] ?>/editar" class="btn btn-outline btn-sm">
                                        <i class="fas fa-edit"></i>
                                        Editar
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>Nenhum PSS encontrado</h4>
                    <p>Crie seu primeiro processo seletivo para começar</p>
                    <a href="/admin/pss/criar-novo" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar Primeiro PSS
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Inscrições Recentes -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-users"></i>
                Inscrições Recentes
            </h3>
            <a href="/admin/inscricoes" class="btn btn-sm btn-outline">Ver Todas</a>
        </div>
        <div class="card-body">
            <?php if (!empty($inscricoes_recentes)): ?>
                <div class="list-group">
                    <?php foreach ($inscricoes_recentes as $inscricao): ?>
                        <div class="list-item">
                            <div class="list-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="list-content">
                                <h4 class="list-title"><?= htmlspecialchars($inscricao['candidato_nome'] ?? 'Candidato não informado') ?></h4>
                                <p class="list-subtitle">
                                    <?php if (!empty($inscricao['protocolo'])): ?>
                                        Protocolo: <?= htmlspecialchars($inscricao['protocolo']) ?>
                                    <?php else: ?>
                                        Protocolo não gerado
                                    <?php endif; ?>
                                </p>
                                <div class="list-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <?php 
                                        $data_inscricao = $inscricao['dt_inscricao'] ?? null;
                                        if ($data_inscricao) {
                                            echo date('d/m/Y H:i', strtotime($data_inscricao));
                                        } else {
                                            echo 'Data não informada';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            <div class="list-actions">
                                <?php 
                                $status = $inscricao['status'] ?? 'indefinido';
                                $badge_class = $status === 'inscrito' ? 'success' : 'secondary';
                                ?>
                                <span class="badge badge-<?= $badge_class ?>">
                                    <?= htmlspecialchars(ucfirst($status)) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h4>Nenhuma inscrição encontrada</h4>
                    <p>As inscrições aparecerão aqui</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gráfico de Inscrições por Mês -->
    <div class="card chart-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-chart-line"></i>
                Inscrições por Mês
            </h3>
            <div class="chart-controls">
                <select class="chart-period">
                    <option value="6">Últimos 6 meses</option>
                    <option value="12">Último ano</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="inscricoesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Configuração do gráfico
const ctx = document.getElementById('inscricoesChart').getContext('2d');
const inscricoesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($meses ?? ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun']) ?>,
        datasets: [{
            label: 'Inscrições',
            data: <?= json_encode($dados_inscricoes ?? [10, 25, 30, 45, 60, 40]) ?>,
            borderColor: '#4CAF50',
            backgroundColor: 'rgba(76, 175, 80, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#4CAF50',
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
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: '#4CAF50',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#757575'
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    color: '#757575'
                }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index'
        }
    }
});
</script>

<style>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--color-border);
}

.welcome-section h1 {
    margin-bottom: 0.5rem;
}

.dashboard-actions {
    display: flex;
    gap: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--color-white);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--color-border);
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: all var(--transition-fast);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--stat-color), var(--stat-color-light));
}

.stat-card.stat-primary {
    --stat-color: var(--color-primary);
    --stat-color-light: var(--color-primary-light);
}

.stat-card.stat-success {
    --stat-color: var(--color-success);
    --stat-color-light: #81C784;
}

.stat-card.stat-warning {
    --stat-color: var(--color-warning);
    --stat-color-light: #FFB74D;
}

.stat-card.stat-info {
    --stat-color: var(--color-info);
    --stat-color-light: #64B5F6;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    background: linear-gradient(135deg, var(--stat-color), var(--stat-color-light));
    box-shadow: var(--shadow-md);
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 0.25rem 0;
    color: var(--color-text);
    line-height: 1;
}

.stat-label {
    margin: 0 0 0.5rem 0;
    color: var(--color-text-light);
    font-size: 1rem;
    font-weight: 600;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-success);
}

.stat-trend i {
    font-size: 0.75rem;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.pss-recentes-card {
    grid-column: 1 / -1;
}

.pss-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.pss-item {
    background: var(--color-gray-50);
    border: 1px solid var(--color-border);
    border-radius: 16px;
    padding: 1.5rem;
    transition: all var(--transition-fast);
}

.pss-item:hover {
    background: var(--color-white);
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
}

.pss-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}

.pss-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.pss-info {
    flex: 1;
}

.pss-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text);
    line-height: 1.3;
}

.pss-subtitle {
    margin: 0;
    font-size: 0.875rem;
    color: var(--color-text-light);
}

.pss-status {
    flex-shrink: 0;
}

.status-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8125rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-success {
    background: rgba(76, 175, 80, 0.1);
    color: var(--color-success);
    border: 1px solid rgba(76, 175, 80, 0.2);
}

.status-secondary {
    background: var(--color-gray-100);
    color: var(--color-gray-600);
    border: 1px solid var(--color-gray-200);
}

.status-badge i {
    font-size: 0.5rem;
}

.pss-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.pss-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    flex: 1;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: var(--color-text-muted);
}

.meta-item i {
    font-size: 0.75rem;
    width: 12px;
    text-align: center;
}

.pss-actions {
    display: flex;
    gap: 0.75rem;
    flex-shrink: 0;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.8125rem;
    border-radius: 8px;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--color-text-light);
}

.empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--color-gray-100);
    color: var(--color-gray-400);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto 1.5rem auto;
}

.empty-state h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text);
}

.empty-state p {
    margin: 0 0 2rem 0;
    font-size: 0.875rem;
}

.list-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.list-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border: 1px solid var(--color-border);
    border-radius: 12px;
    background: var(--color-gray-50);
    transition: all var(--transition-fast);
}

.list-item:hover {
    background: var(--color-white);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.list-avatar {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--color-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.list-content {
    flex: 1;
}

.list-title {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-text);
}

.list-subtitle {
    margin: 0 0 0.5rem 0;
    font-size: 0.875rem;
    color: var(--color-text-light);
}

.list-meta {
    display: flex;
    gap: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    color: var(--color-text-muted);
}

.meta-item i {
    font-size: 0.625rem;
}

.list-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.badge-success {
    background: rgba(76, 175, 80, 0.1);
    color: var(--color-success);
}

.badge-secondary {
    background: var(--color-gray-100);
    color: var(--color-gray-600);
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--color-text-light);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: var(--color-gray-400);
}

.empty-state h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--color-text);
}

.empty-state p {
    margin: 0 0 1.5rem 0;
    font-size: 0.875rem;
}

.chart-card {
    grid-column: 1 / -1;
}

.chart-controls {
    display: flex;
    gap: 1rem;
}

.chart-period {
    padding: 0.5rem 1rem;
    border: 1px solid var(--color-border);
    border-radius: 6px;
    background: var(--color-white);
    font-size: 0.875rem;
    color: var(--color-text);
}

.chart-container {
    height: 300px;
    position: relative;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 1.5rem;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }

    .stat-number {
        font-size: 2rem;
    }

    .pss-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .pss-status {
        align-self: flex-start;
    }

    .pss-details {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .pss-actions {
        justify-content: center;
    }

    .empty-state {
        padding: 3rem 1rem;
    }
}

@media (max-width: 1024px) and (min-width: 769px) {
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }
}
</style>

