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

    <!-- ✅ CORREÇÃO: PSS Disponíveis sempre visível -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-clipboard-list"></i>
                Processos Seletivos Disponíveis
            </h2>
            <p class="section-subtitle">Processos abertos para inscrição</p>
        </div>
        
        <?php if (!empty($pss_disponiveis)): ?>
        <div class="pss-grid">
            <?php foreach ($pss_disponiveis as $pss): ?>
            <div class="pss-card">
                <div class="pss-header">
                    <h3 class="pss-title"><?= htmlspecialchars($pss['titulo']) ?></h3>
                    <?php
                    // ✅ CORREÇÃO: Determinar status do PSS baseado nas datas
                    $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
                    $status_badge = 'status-aberto';
                    $status_texto = 'Aberto';
                    
                    if (!empty($pss['inscricao_ini']) && !empty($pss['inscricao_fim'])) {
                        try {
                            $inicio = new DateTime($pss['inscricao_ini']);
                            $inicio->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                            
                            $fim = new DateTime($pss['inscricao_fim']);
                            $fim->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                            
                            if ($agora < $inicio) {
                                $status_badge = 'status-em-breve';
                                $status_texto = 'Em Breve';
                            } elseif ($agora > $fim) {
                                $status_badge = 'status-encerrado';
                                $status_texto = 'Encerrado';
                            }
                        } catch (Exception $e) {
                            // Manter status padrão em caso de erro
                        }
                    }
                    ?>
                    <span class="pss-badge <?= $status_badge ?>"><?= $status_texto ?></span>
                </div>
                <div class="pss-info">
                    <?php if (!empty($pss['descricao'])): ?>
                    <p class="pss-description"><?= htmlspecialchars(substr($pss['descricao'], 0, 100)) ?>...</p>
                    <?php endif; ?>
                    
                    <div class="pss-stats">
                        <?php if (isset($pss['total_vagas']) && $pss['total_vagas'] > 0): ?>
                        <span class="stat-badge">
                            <i class="fas fa-users"></i>
                            <?= $pss['total_vagas'] ?> vagas
                        </span>
                        <?php endif; ?>
                        
                        <?php if (isset($pss['total_cargos']) && $pss['total_cargos'] > 0): ?>
                        <span class="stat-badge">
                            <i class="fas fa-briefcase"></i>
                            <?= $pss['total_cargos'] ?> cargos
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pss-dates">
                        <?php if (!empty($pss['inscricao_ini'])): ?>
                        <span class="date-item">
                            <i class="fas fa-calendar-alt"></i>
                            Início: <?php
                            try {
                                $data_inicio = new DateTime($pss['inscricao_ini']);
                                $data_inicio->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                                echo $data_inicio->format('d/m/Y H:i');
                            } catch (Exception $e) {
                                echo 'Data não disponível';
                            }
                            ?>
                        </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($pss['inscricao_fim'])): ?>
                        <span class="date-item">
                            <i class="fas fa-calendar-times"></i>
                            Fim: <?php
                            try {
                                $data_fim = new DateTime($pss['inscricao_fim']);
                                $data_fim->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                                echo $data_fim->format('d/m/Y H:i');
                            } catch (Exception $e) {
                                echo 'Data não disponível';
                            }
                            ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pss-actions">
                    <a href="/pss/<?= $pss['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-info-circle"></i> Ver Detalhes
                    </a>
                    
                    <?php if ($status_texto === 'Aberto'): ?>
                    <a href="/onboarding/<?= $pss['id'] ?>/zona" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Inscreva-se
                    </a>
                    <?php elseif ($status_texto === 'Em Breve'): ?>
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-clock"></i> Em Breve
                    </button>
                    <?php else: ?>
                    <button class="btn btn-secondary btn-sm" disabled>
                        <i class="fas fa-times"></i> Encerrado
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>Nenhum processo disponível</h3>
            <p>No momento não há processos seletivos abertos para inscrição.</p>
            <a href="/" class="btn btn-primary">Ver Todos os Processos</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- ✅ CORREÇÃO: Minhas Inscrições sempre visível -->
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
                <?php if (!empty($pss_disponiveis)): ?>
                <p>Veja os processos disponíveis acima e faça sua inscrição!</p>
                <?php endif; ?>
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
                                // ✅ CORREÇÃO: Verificar múltiplos campos de data
                                $data_inscricao = $inscricao['dt_inscricao'] ?? $inscricao['data_inscricao'] ?? $inscricao['criado_em'] ?? null;
                                if ($data_inscricao) {
                                    try {
                                        $data = new DateTime($data_inscricao);
                                        $data->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                                        echo $data->format('d/m/Y H:i');
                                    } catch (Exception $e) {
                                        echo 'Data não disponível';
                                    }
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
                        // ✅ CORREÇÃO: Status mais abrangente
                        $statusClass = '';
                        $statusLabel = '';
                        $status = $inscricao['status'] ?? 'pendente';
                        switch($status) {
                            case 'apta':
                            case 'aprovada':
                                $statusClass = 'success';
                                $statusLabel = 'Apta';
                                break;
                            case 'inscrito':
                            case 'inscrita':
                                $statusClass = 'info';
                                $statusLabel = 'Inscrito';
                                break;
                            case 'documentos_completos':
                            case 'documentos_ok':
                                $statusClass = 'success';
                                $statusLabel = 'Documentos OK';
                                break;
                            case 'em_avaliacao':
                            case 'avaliacao':
                                $statusClass = 'warning';
                                $statusLabel = 'Em Avaliação';
                                break;
                            case 'aprovado':
                            case 'aprovada':
                                $statusClass = 'success';
                                $statusLabel = 'Aprovado';
                                break;
                            case 'reprovado':
                            case 'reprovada':
                                $statusClass = 'danger';
                                $statusLabel = 'Reprovado';
                                break;
                            case 'cancelado':
                            case 'cancelada':
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
                            case 'ativa':
                                $statusClass = 'success';
                                $statusLabel = 'Ativa';
                                break;
                            default:
                                $statusClass = 'secondary';
                                $statusLabel = ucfirst($status);
                        }
                        ?>
                        <span class="status-badge status-<?= $statusClass ?>"><?= $statusLabel ?></span>
                    </div>
                    <div class="inscricao-actions">
                        <?php if (isset($inscricao['id'])): ?>
                        <a href="/painel/inscricao/<?= $inscricao['id'] ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                        <?php endif; ?>
                        
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

    <!-- ✅ NOVO: Ações Rápidas -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-bolt"></i>
                Ações Rápidas
            </h2>
        </div>
        
        <div class="quick-actions">
            <a href="/painel/dados-usuario" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="action-content">
                    <h3>Meus Dados</h3>
                    <p>Atualizar informações pessoais</p>
                </div>
            </a>
            
            <a href="/painel/inscricoes" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="action-content">
                    <h3>Todas as Inscrições</h3>
                    <p>Ver histórico completo</p>
                </div>
            </a>
            
            <a href="/" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="action-content">
                    <h3>Buscar Processos</h3>
                    <p>Encontrar novas oportunidades</p>
                </div>
            </a>
            
            <a href="/painel/alterar-senha" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="action-content">
                    <h3>Alterar Senha</h3>
                    <p>Segurança da conta</p>
                </div>
            </a>
        </div>
    </div>
</section>

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
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
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
    margin-right: 10px;
}

.pss-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
}

.status-aberto {
    background: #d4edda;
    color: #155724;
}

.status-em-breve {
    background: #fff3cd;
    color: #856404;
}

.status-encerrado {
    background: #f8d7da;
    color: #721c24;
}

.pss-description {
    color: #666;
    margin: 0 0 15px 0;
    line-height: 1.5;
    font-size: 0.9rem;
}

.pss-stats {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.stat-badge {
    display: flex;
    align-items: center;
    gap: 5px;
    background: #f8f9fa;
    color: #495057;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.pss-dates {
    margin-bottom: 15px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 5px;
}

.pss-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
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
    flex-wrap: wrap;
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

.inscricao-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

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

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
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
    
    .pss-grid {
        grid-template-columns: 1fr;
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
    
    .pss-actions {
        justify-content: flex-start;
    }
    
    .inscricao-actions {
        justify-content: flex-start;
    }
}
</style>
