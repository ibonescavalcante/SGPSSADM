<?php $this->layout('/painel/painel-template') ?>

<section class="candidato-dashboard">
    <div class="dashboard-header">
        <div class="welcome-section">
            <h1 class="welcome-title">Bem-vindo, <?= htmlspecialchars($candidato['nome'] ?? 'Candidato') ?>!</h1>
            <p class="welcome-subtitle">Gerencie suas inscrições e acompanhe o andamento dos processos seletivos</p>
        </div>
        
        <div class="quick-stats">
            <div class="stat-item">
                <div class="stat-number"><?= count($inscricoes ?? []) ?></div>
                <div class="stat-label">Inscrições</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">
                    <?php
                    $ativas = 0;
                    foreach ($inscricoes ?? [] as $inscricao) {
                        if (in_array($inscricao['status'], ['apta', 'inscrito', 'documentos_completos', 'em_avaliacao'])) {
                            $ativas++;
                        }
                    }
                    echo $ativas;
                    ?>
                </div>
                <div class="stat-label">Ativas</div>
            </div>
        </div>
    </div>

    <!-- PSS Disponíveis -->
    <?php if (!empty($pss_disponiveis)): ?>
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-clipboard-list"></i>
                Processos Seletivos Disponíveis
            </h2>
            <p class="section-subtitle">Novos processos abertos para inscrição</p>
        </div>
        
        <div class="pss-grid">
            <?php foreach ($pss_disponiveis as $pss): ?>
            <div class="pss-card">
                <div class="pss-header">
                    <h3 class="pss-title"><?= htmlspecialchars($pss['titulo']) ?></h3>
                    <span class="pss-badge status-aberto">Aberto</span>
                </div>
                <div class="pss-info">
                    <p class="pss-description"><?= htmlspecialchars(substr($pss['descricao'], 0, 100)) ?>...</p>
                    <div class="pss-dates">
                        <span class="date-item">
                            <i class="fas fa-calendar-alt"></i>
                            Inscrições até: <?= date('d/m/Y', strtotime($pss['inscricao_fim'])) ?>
                        </span>
                    </div>
                </div>
                <div class="pss-actions">
                    <a href="/onboarding/<?= $pss['id'] ?>/zona" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Inscreva-se
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Minhas Inscrições -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-list-alt"></i>
                Minhas Inscrições
            </h2>
            <p class="section-subtitle">Acompanhe o status das suas inscrições</p>
        </div>
        
        <div class="inscricoes-container">
            <?php if (empty($inscricoes)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Nenhuma inscrição encontrada</h3>
                <p>Você ainda não se inscreveu em nenhum processo seletivo.</p>
                <a href="/pss" class="btn btn-primary">Ver Processos Disponíveis</a>
            </div>
            <?php else: ?>
            <div class="inscricoes-list">
                <?php foreach (array_slice($inscricoes, 0, 5) as $inscricao): ?>
                <div class="inscricao-item">
                    <div class="inscricao-info">
                        <h4 class="inscricao-title"><?= htmlspecialchars($inscricao['pss_titulo'] ?? 'PSS não informado') ?></h4>
                        <p class="inscricao-cargo"><?= htmlspecialchars($inscricao['cargo_nome'] ?? 'Cargo não informado') ?></p>
                        <div class="inscricao-meta">
                            <span class="meta-item">
                                <i class="fas fa-calendar"></i>
                                Inscrito em: 
                                <?php 
                                // Verificar se existe dt_inscricao ou data_inscricao
                                $data_inscricao = $inscricao['dt_inscricao'] ?? $inscricao['data_inscricao'] ?? null;
                                if ($data_inscricao) {
                                    echo date('d/m/Y', strtotime($data_inscricao));
                                } else {
                                    echo 'Data não informada';
                                }
                                ?>
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-hashtag"></i>
                                Protocolo: <?= htmlspecialchars($inscricao['protocolo'] ?? 'Não informado') ?>
                            </span>
                        </div>
                    </div>
                    <div class="inscricao-status">
                        <?php
                        $statusClass = '';
                        $statusLabel = '';
                        $status = $inscricao['status'] ?? 'pendente';
                        switch($status) {
                            case 'apta':
                                $statusClass = 'success';
                                $statusLabel = 'Apta';
                                break;
                            case 'inscrito':
                                $statusClass = 'info';
                                $statusLabel = 'Inscrito';
                                break;
                            case 'documentos_completos':
                                $statusClass = 'success';
                                $statusLabel = 'Documentos OK';
                                break;
                            case 'em_avaliacao':
                                $statusClass = 'warning';
                                $statusLabel = 'Em Avaliação';
                                break;
                            case 'aprovado':
                                $statusClass = 'success';
                                $statusLabel = 'Aprovado';
                                break;
                            case 'reprovado':
                                $statusClass = 'danger';
                                $statusLabel = 'Reprovado';
                                break;
                            case 'cancelado':
                                $statusClass = 'secondary';
                                $statusLabel = 'Cancelado';
                                break;
                            case 'pendente':
                                $statusClass = 'warning';
                                $statusLabel = 'Pendente';
                                break;
                            case 'inapta':
                                $statusClass = 'danger';
                                $statusLabel = 'Inapta';
                                break;
                            default:
                                $statusClass = 'secondary';
                                $statusLabel = ucfirst($status);
                        }
                        ?>
                        <span class="status-badge status-<?= $statusClass ?>"><?= $statusLabel ?></span>
                    </div>
                    <div class="inscricao-actions">
                        <a href="/painel/inscricao/<?= $inscricao['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                        <?php if (!empty($inscricao['protocolo'])): ?>
                        <a href="/inscricao/comprovante/<?= $inscricao['protocolo'] ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-certificate"></i> Comprovante
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($inscricoes) > 5): ?>
            <div class="view-all">
                <a href="/painel/inscricoes" class="btn btn-outline-secondary">
                    Ver Todas as Inscrições (<?= count($inscricoes) ?>)
                </a>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

<style>
.candidato-dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-header {
    background: linear-gradient(120deg, #16a34a 0%, #22c55e 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.welcome-title {
    font-size: 2rem;
    margin: 0 0 10px 0;
    font-weight: 600;
}

.welcome-subtitle {
    margin: 0;
    opacity: 0.9;
}

.quick-stats {
    display: flex;
    gap: 30px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.8;
    margin-top: 5px;
}

.dashboard-section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.section-header {
    margin-bottom: 20px;
}

.section-title {
    font-size: 1.5rem;
    margin: 0 0 5px 0;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-subtitle {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.pss-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.pss-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.pss-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.pss-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.pss-title {
    font-size: 1.1rem;
    margin: 0;
    color: #333;
    flex: 1;
}

.pss-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-aberto {
    background: #d4edda;
    color: #155724;
}

.pss-description {
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
}

.pss-dates {
    margin-bottom: 15px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #666;
}

.pss-actions {
    text-align: right;
}

.inscricoes-list {
    space-y: 15px;
}

.inscricao-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.inscricao-info {
    flex: 1;
}

.inscricao-title {
    font-size: 1.1rem;
    margin: 0 0 5px 0;
    color: #333;
}

.inscricao-cargo {
    color: #666;
    margin: 0 0 10px 0;
    font-weight: 500;
}

.inscricao-meta {
    display: flex;
    gap: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.85rem;
    color: #666;
}

.inscricao-status {
    margin: 0 20px;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-info { background: #d1ecf1; color: #0c5460; }
.status-success { background: #d4edda; color: #155724; }
.status-warning { background: #fff3cd; color: #856404; }
.status-danger { background: #f8d7da; color: #721c24; }
.status-secondary { background: #e2e3e5; color: #383d41; }

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.action-card {
    display: flex;
    align-items: center;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
}

.action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
}

.action-icon {
    font-size: 2rem;
    color: #16a34a;
    margin-right: 15px;
}

.action-content h3 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
}

.action-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #ccc;
}

.empty-state h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    text-align: center;
    transition: all 0.2s;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: #16a34a;
    color: white;
}

.btn-primary:hover {
    background: #15803d;
    color: white;
    text-decoration: none;
}

.btn-outline-primary {
    border: 1px solid #16a34a;
    color: #16a34a;
    background: transparent;
}

.btn-outline-primary:hover {
    background: #16a34a;
    color: white;
    text-decoration: none;
}

.btn-outline-success {
    border: 1px solid #22c55e;
    color: #22c55e;
    background: transparent;
}

.btn-outline-success:hover {
    background: #22c55e;
    color: white;
    text-decoration: none;
}

.btn-outline-secondary {
    border: 1px solid #6c757d;
    color: #6c757d;
    background: transparent;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    text-decoration: none;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.875rem;
}

.view-all {
    text-align: center;
    margin-top: 20px;
}

@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .quick-stats {
        justify-content: center;
    }
    
    .inscricao-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .inscricao-meta {
        flex-direction: column;
        gap: 5px;
    }
}
</style>

