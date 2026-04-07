<?php $this->layout("/painel/painel-template") ?>

<?php
// Verificar se o prazo de inscrição ainda está aberto
$agora = new DateTime();
$fim_inscricao = new DateTime($pss['inscricao_fim'] ?? '1970-01-01');
$prazo_aberto = $agora <= $fim_inscricao;

// Verificar se está cancelada (compatível com e sem campos de cancelamento)
$esta_cancelada = false;
if (isset($inscricao['cancelada'])) {
    $esta_cancelada = $inscricao['cancelada'] == true || $inscricao['cancelada'] == 1;
} else {
    $esta_cancelada = in_array($inscricao['status'], ['cancelado', 'inapta']);
}

$pode_cancelar = $prazo_aberto && !$esta_cancelada;
?>

<style>
    .detalhes-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: #f8f9fa;
        min-height: 100vh;
    }

    .page-header {
        background: #28a745;
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(40, 167, 69, 0.2);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(50%, -50%);
    }

    .page-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-header .subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
    }

    .protocol-badge {
        background: #28a745;
        padding: 12px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1.1rem;
        margin-top: 20px;
        display: inline-block;
        color: white;
        border: none;
    }

    .cards-grid {
        display: grid;
        gap: 25px;
        margin-bottom: 30px;
    }

    .info-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        border: 1px solid #e9ecef;
        margin-bottom: 20px;
    }

    .card-header {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
        color: #495057;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-header i {
        color: #28a745;
        font-size: 1.2rem;
    }

    .card-body {
        padding: 20px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-item label {
        font-weight: 600;
        color: #495057;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-item span {
        color: #212529;
        font-size: 1rem;
        padding: 8px 16px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        min-height: 20px;
        display: flex;
        align-items: center;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        max-width: 100%;
        word-wrap: break-word;
        white-space: nowrap;
    }

    .status-ativa {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-pendente {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .status-cancelada {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .documents-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .document-category {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }

    .category-title {
        background: linear-gradient(120deg, #28a745 0%, #20c997 100%);
        color: white;
        margin: 0;
        padding: 15px 20px;
        font-size: 1rem;
        font-weight: 600;
        border-bottom: 1px solid #e9ecef;
    }

    .category-files {
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .file-card {
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.2s ease;
    }

    .file-card:hover {
        border-color: #28a745;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.1);
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
        min-width: 0;
    }

    .file-icon {
        width: 40px;
        height: 40px;
        background: #28a745;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .file-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: #495057;
        font-size: 0.95rem;
        word-break: break-word;
        overflow-wrap: break-word;
        line-height: 1.3;
    }

    .file-count {
        font-size: 0.8rem;
        color: #6c757d;
    }

    .file-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }


    .document-actions .btn {
        flex-grow: 1; /* Faz os botões ocuparem o espaço disponível */
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: #dee2e6;
    }

    .empty-state p {
        font-size: 1.1rem;
        margin: 0;
    }

    .actions-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
    }

    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        text-align: center;
    }

    .btn-primary {
        background: #1e7e34;
        color: white;
        border: none;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
        border: none;
    }

    .btn-outline-secondary {
        background: transparent;
        color: #6c757d;
        border: 2px solid #6c757d;
    }

    .btn-outline-secondary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-sm {
        padding: 8px 16px;
        font-size: 0.85rem;
    }

    .btn:hover:not(:disabled) {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .alert {
        padding: 20px;
        border-radius: 12px;
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 15px;
        border-left: 4px solid;
    }

    .alert-info {
        background: #e3f2fd;
        color: #0d47a1;
        border-left-color: #2196f3;
    }

    .alert-secondary {
        background: #f5f5f5;
        color: #424242;
        border-left-color: #9e9e9e;
    }

    .alert-danger {
        background: #ffebee;
        color: #c62828;
        border-left-color: #f44336;
    }

    .alert-warning {
        background: #fff8e1;
        color: #f57c00;
        border-left-color: #ff9800;
    }

    .alert i {
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.6);
        backdrop-filter: blur(5px);
    }

    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .modal-dialog {
        max-width: 500px;
        width: 90%;
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .modal-header {
        padding: 25px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 600;
        color: #495057;
    }

    .btn-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #6c757d;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .btn-close:hover {
        background: #e9ecef;
        color: #495057;
    }

    .modal-body {
        padding: 25px;
        font-size: 1rem;
        line-height: 1.6;
        color: #495057;
    }

    .modal-footer {
        padding: 25px;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .detalhes-container {
            padding: 15px;
        }

        .page-header {
            padding: 25px 20px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2rem;
            flex-direction: column;
            gap: 10px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .file-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .file-info {
            width: 100%;
        }

        .file-actions {
            width: 100%;
            justify-content: center;
        }

        .actions-grid {
            grid-template-columns: 1fr;
        }

        .modal-dialog {
            width: 95%;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            padding: 20px;
        }
    }

    @media (max-width: 480px) {
        .page-header h1 {
            font-size: 1.5rem;
        }

        .card-body {
            padding: 20px;
        }

        .btn {
            padding: 12px 20px;
            font-size: 0.9rem;
        }
    }
</style>

<div class="detalhes-container">
    <!-- Header da Página -->
    <div class="page-header">
        <h1>
            <i class="fas fa-file-alt"></i>
            Detalhes da Inscrição
        </h1>
        <p class="subtitle">Visualize todas as informações da sua inscrição</p>
        <div class="protocol-badge">
            <i class="fas fa-barcode"></i>
            Protocolo: <?= htmlspecialchars($inscricao["protocolo"] ?? "N/A") ?>
        </div>
    </div>

    <?php if (isset($inscricao)): ?>
        <div class="cards-grid">
            <!-- Informações do PSS -->
            <div class="info-card">
                <div class="card-header">
                    <h2><i class="fas fa-clipboard-list"></i> Processo Seletivo</h2>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item full-width">
                            <label>PSS</label>
                            <span><?= htmlspecialchars($pss["titulo"] ?? $inscricao["pss_titulo"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Cargo</label>
                            <span><?= htmlspecialchars($cargo["nome"] ?? $inscricao["cargo_nome"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Zona</label>
                            <span><?= htmlspecialchars($cargo["zona"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Microrregião</label>
                            <span><?= htmlspecialchars($cargo["microrregiao"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Total de Vagas</label>
                            <span><?= htmlspecialchars($cargo["vagas_total"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Vagas PCD</label>
                            <span><?= htmlspecialchars($cargo["vagas_pcd"] ?? "0") ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status da Inscrição -->
            <div class="info-card">
                <div class="card-header">
                    <h2><i class="fas fa-info-circle"></i> Status da Inscrição</h2>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Data da Inscrição</label>
                            <span>
                                <?php 
                                $data_inscricao = $inscricao["dt_inscricao"] ?? $inscricao["data_inscricao"] ?? null;
                                if ($data_inscricao) {
                                    echo date("d/m/Y H:i", strtotime($data_inscricao));
                                } else {
                                    echo "Data não informada";
                                }
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Status Atual</label>
                            <span style="display: flex; align-items: center;">
                                <span class="status-badge status-<?= strtolower($inscricao["status"] ?? "pendente") ?>">
                                    <?php
                                    if ($esta_cancelada) {
                                        echo "<i class='fas fa-times-circle'></i> Cancelada";
                                    } else {
                                        $status_display = $inscricao["status"] ?? "Pendente";
                                        $icon = $status_display === "ativa" ? "check-circle" : "clock";
                                        echo "<i class='fas fa-{$icon}'></i> " . htmlspecialchars(ucfirst($status_display));
                                    }
                                    ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Concorre a Vagas PCD</label>
                            <span>
                                <?php 
                                // Verificar no extra_json primeiro, depois nos campos diretos
                                $valor_pcd = "nao"; // valor padrão
                                
                                // Tentar ler do extra_json
                                if (isset($inscricao['extra_json'])) {
                                    $extra_data = json_decode($inscricao['extra_json'], true);
                                    if (isset($extra_data['concorrer_pcd'])) {
                                        $valor_pcd = $extra_data['concorrer_pcd'];
                                    }
                                }
                                
                                // Fallback para campos diretos
                                if ($valor_pcd === "nao") {
                                    $valor_pcd = $inscricao["concorrer_pcd"] ?? $inscricao["concorre_pcd"] ?? $inscricao["pcd"] ?? "nao";
                                }
                                
                                // Verificar diferentes formatos possíveis
                                $concorre_pcd = false;
                                if ($valor_pcd === "sim" || $valor_pcd === "1" || $valor_pcd === 1 || $valor_pcd === true || $valor_pcd === "true") {
                                    $concorre_pcd = true;
                                }
                                
                                echo $concorre_pcd ? "<i class='fas fa-check' style='color: #28a745;'></i> Sim" : "<i class='fas fa-times' style='color: #6c757d;'></i> Não";
                                ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Alertas sobre prazo e status -->
                    <?php if (!$prazo_aberto && !$esta_cancelada): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Prazo encerrado</strong><br>
                                O prazo de inscrições foi encerrado. Não é mais possível cancelar ou editar esta inscrição.
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($esta_cancelada): ?>
                        <div class="alert alert-secondary">
                            <i class="fas fa-ban"></i>
                            <div>
                                <strong>Inscrição cancelada</strong><br>
                                Esta inscrição foi cancelada pelo candidato.
                                <?php if (isset($inscricao['data_cancelamento']) && $inscricao['data_cancelamento']): ?>
                                    <br><small>Cancelada em: <?= date('d/m/Y H:i', strtotime($inscricao['data_cancelamento'])) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dados do Candidato -->
            <div class="info-card">
                <div class="card-header">
                    <h2><i class="fas fa-user"></i> Dados do Candidato</h2>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Nome Completo</label>
                            <span><?= htmlspecialchars($candidato["nome"] ?? $inscricao["candidato_nome"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>CPF</label>
                            <span><?= htmlspecialchars($candidato["cpf"] ?? $inscricao["candidato_cpf"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Identificação</label>
                            <span>
                                <?php
                                if (!empty($candidato["documento_tipo"]) && !empty($candidato["documento_numero"])) {
                                    echo htmlspecialchars($candidato["documento_tipo"] . ": " . $candidato["documento_numero"]);
                                } else {
                                    echo "N/A";
                                }
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Data de Nascimento</label>
                            <span>
                                <?php 
                                if (!empty($candidato["data_nascimento"])) {
                                    echo date("d/m/Y", strtotime($candidato["data_nascimento"]));
                                } else {
                                    echo "N/A";
                                }
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Gênero</label>
                            <span><?= htmlspecialchars($candidato["genero_nome"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Telefone</label>
                            <span><?= htmlspecialchars($candidato["celular"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>E-mail</label>
                            <span><?= htmlspecialchars($candidato["email"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item full-width">
                            <label>Endereço</label>
                            <span>
                                <?php
                                $endereco_parts = [];
                                if (!empty($candidato["endereco"])) $endereco_parts[] = $candidato["endereco"];
                                if (!empty($candidato["numero"])) $endereco_parts[] = "nº " . $candidato["numero"];
                                if (!empty($candidato["complemento"])) $endereco_parts[] = $candidato["complemento"];
                                if (!empty($candidato["bairro"])) $endereco_parts[] = $candidato["bairro"];
                                if (!empty($candidato["cidade"])) $endereco_parts[] = $candidato["cidade"];
                                if (!empty($candidato["uf"])) $endereco_parts[] = $candidato["uf"];
                                if (!empty($candidato["cep"])) $endereco_parts[] = "CEP: " . $candidato["cep"];
                                
                                echo !empty($endereco_parts) ? implode(", ", $endereco_parts) : "N/A";
                                ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Escolaridade</label>
                            <span><?= htmlspecialchars($candidato["escolaridade_nome"] ?? "N/A") ?></span>
                        </div>
                        <div class="info-item">
                            <label>Naturalidade</label>
                            <span>
                                <?php
                                $naturalidade_parts = [];
                                if (!empty($candidato["naturalidade_cidade_nome"])) $naturalidade_parts[] = $candidato["naturalidade_cidade_nome"];
                                if (!empty($candidato["naturalidade_estado_nome"])) $naturalidade_parts[] = $candidato["naturalidade_estado_nome"];
                                echo !empty($naturalidade_parts) ? implode(" - ", $naturalidade_parts) : "N/A";
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos -->
            <div class="info-card">
                <div class="card-header">
                    <h2><i class="fas fa-file-pdf"></i> Documentos Enviados</h2>
                </div>
                <div class="card-body">

                    <?php 
                    $documentos_enviados = [];
                    if (isset($inscricao['extra_json'])) {
                        $extra_data = json_decode($inscricao['extra_json'], true);
                        if (isset($extra_data['documentos']) && is_array($extra_data['documentos'])) {
                            foreach ($extra_data['documentos'] as $tipo => $caminhos) {
                                if (!empty($caminhos)) {
                                    // Se for um array de caminhos (múltiplos arquivos)
                                    if (is_array($caminhos)) {
                                        $documentos_enviados[$tipo] = [];
                                        foreach ($caminhos as $index => $caminho) {
                                            $documentos_enviados[$tipo][] = [
                                                'path' => $caminho,
                                                'name' => basename($caminho),
                                                'index' => $index
                                            ];
                                        }
                                    } else {
                                        // Se for um único arquivo
                                        $documentos_enviados[$tipo] = [[
                                            'path' => $caminhos,
                                            'name' => basename($caminhos),
                                            'index' => 0
                                        ]];
                                    }
                                }
                            }
                        }
                    }

                    // Mapear nomes amigáveis para os tipos de documentos
                    $nomes_documentos = [
                        'documento_identidade' => 'Documento de Identidade',
                        'comprovante_escolaridade' => 'Comprovante de Escolaridade',
                        'comprovante_experiencia_declaracoes' => 'Comprovante de Experiência ou Declarações',
                        'certificados' => 'Certificados',
                        'CNH_B_EAR' => 'CNH Categoria B com EAR',
                        'CNH_D_EAR' => 'CNH Categoria D com EAR',
                        'CURSO_CONDUTOR_EMERGENCIA' => 'Curso Condutor de Veículos de Emergência',
                        'CURSO_CONDUTOR_ESCOLAR' => 'Curso Condutor de Transporte Escolar',
                        'HABILITACAO_MAQUINAS_PESADAS' => 'Habilitação Operador de Máquinas Pesadas'
                    ];
                    ?>
                    <?php if (!empty($documentos_enviados)): ?>
                        <div class="documents-container">
                            <?php foreach ($documentos_enviados as $tipo => $arquivos): ?>
                                <?php 
                                $nome_documento = $nomes_documentos[$tipo] ?? ucwords(str_replace("_", " ", $tipo));
                                ?>
                                <div class="document-category">
                                    <h4 class="category-title"><?= htmlspecialchars($nome_documento) ?></h4>
                                    <div class="category-files">
                                        <?php foreach ($arquivos as $arquivo): ?>
                                            <div class="file-card">
                                                <div class="file-info">
                                                    <div class="file-icon">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </div>
                                                    <div class="file-details">
                                                        <span class="file-name"><?= htmlspecialchars($arquivo["name"]) ?></span>
                                                        <?php if (count($arquivos) > 1): ?>
                                                            <span class="file-count">Arquivo <?= $arquivo['index'] + 1 ?> de <?= count($arquivos) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="file-actions">
                                                    <?php
                                                    // **CORREÇÃO DEFINITIVA - Trata todos os tipos de caminhos do banco**
                                                    $raw_path = $arquivo["path"];
                                                    
                                                    // Extrair apenas a parte relevante do caminho
                                                    // Procurar por "uploads/documentos" ou "documentos" no caminho
                                                    if (preg_match('/.*[\/\\\\]?(uploads[\/\\\\]documentos[\/\\\\].*)$/', $raw_path, $matches)) {
                                                        // Caso: caminho contém "uploads/documentos"
                                                        $caminho_relativo = $matches[1];
                                                    } elseif (preg_match('/.*[\/\\\\]?(documentos[\/\\\\].*)$/', $raw_path, $matches)) {
                                                        // Caso: caminho contém apenas "documentos"
                                                        $caminho_relativo = 'uploads/' . $matches[1];
                                                    } else {
                                                        // Fallback: assumir que é um caminho relativo
                                                        $caminho_relativo = ltrim($raw_path, '/\\');
                                                        if (!str_starts_with($caminho_relativo, 'uploads/')) {
                                                            $caminho_relativo = 'uploads/' . $caminho_relativo;
                                                        }
                                                    }
                                                    
                                                    // Normalizar separadores para web
                                                    $caminho_relativo = str_replace('\\', '/', $caminho_relativo);
                                                    
                                                    // Construir URL final
                                                    $final_path = '/' . $caminho_relativo;
                                                    ?>
                                                    <a href="<?= htmlspecialchars($final_path) ?>" class="btn btn-sm btn-primary" target="_blank">
                                                        <i class="fas fa-eye"></i> Visualizar
                                                    </a>
                                                    
                                                    <a href="<?= htmlspecialchars($final_path) ?>" class="btn btn-sm btn-secondary" download>
                                                        <i class="fas fa-download"></i> Baixar
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Nenhum documento anexado a esta inscrição.</p>
                        </div>
                    <?php endif; ?>

                    <?php 
                    // Verificar se há laudo PCD no extra_json
                    $laudo_pcd_path_raw = null;
                    if (isset($inscricao['extra_json'])) {
                        $extra_data = json_decode($inscricao['extra_json'], true);
                        $laudo_pcd_path_raw = $extra_data['laudo_pcd'] ?? null;
                    }
                    // Fallback para campo direto (compatibilidade)
                    if (empty($laudo_pcd_path_raw) && !empty($inscricao["laudo_pcd"])) {
                        $laudo_pcd_path_raw = $inscricao["laudo_pcd"];
                    }
                    ?>
                    <?php if (!empty($laudo_pcd_path_raw)): ?>
                        <?php
                        // **CORREÇÃO DEFINITIVA - Mesma lógica para laudo PCD**
                        if (preg_match('/.*[\/\\\\]?(uploads[\/\\\\]documentos[\/\\\\].*)$/', $laudo_pcd_path_raw, $matches)) {
                            $caminho_relativo_laudo = $matches[1];
                        } elseif (preg_match('/.*[\/\\\\]?(documentos[\/\\\\].*)$/', $laudo_pcd_path_raw, $matches)) {
                            $caminho_relativo_laudo = 'uploads/' . $matches[1];
                        } else {
                            $caminho_relativo_laudo = ltrim($laudo_pcd_path_raw, '/\\');
                            if (!str_starts_with($caminho_relativo_laudo, 'uploads/')) {
                                $caminho_relativo_laudo = 'uploads/' . $caminho_relativo_laudo;
                            }
                        }
                        
                        $caminho_relativo_laudo = str_replace('\\', '/', $caminho_relativo_laudo);
                        $final_laudo_path = '/' . $caminho_relativo_laudo;
                        ?>
                        <div class="document-category">
                            <h4 class="category-title">Laudo Médico PCD</h4>
                            <div class="category-files">
                                <div class="file-card">
                                    <div class="file-info">
                                        <div class="file-icon" style="background: #17a2b8;">
                                            <i class="fas fa-file-medical"></i>
                                        </div>
                                        <div class="file-details">
                                            <span class="file-name"><?= htmlspecialchars(basename($laudo_pcd_path_raw)) ?></span>
                                            <span class="file-count">Documento para concorrer às vagas PCD</span>
                                        </div>
                                    </div>
                                    <div class="file-actions">
                                        <a href="<?= htmlspecialchars($final_laudo_path) ?>" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Visualizar
                                        </a>
                                        <a href="<?= htmlspecialchars($final_laudo_path) ?>" download class="btn btn-sm btn-secondary">
                                            <i class="fas fa-download"></i> Baixar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ações -->
            <div class="actions-card">
                <a href="/painel" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar ao Painel
                </a>
                
                <?php if (!$esta_cancelada): ?>
                    <a href="/inscricao/comprovante/<?= htmlspecialchars($inscricao["protocolo"] ?? "") ?>" class="btn btn-primary" target="_blank">
                        <i class="fas fa-certificate"></i> Ver Comprovante
                    </a>
                <?php endif; ?>
                
                <?php if ($pode_cancelar): ?>
                    <button type="button" class="btn btn-danger" onclick="confirmarCancelamento('<?= $inscricao['id'] ?>', '<?= htmlspecialchars($pss['titulo'] ?? $inscricao['pss_titulo']) ?>')">
                        <i class="fas fa-times"></i> Cancelar Inscrição
                    </button>
                <?php elseif (!$prazo_aberto && !$esta_cancelada): ?>
                    <button type="button" class="btn btn-outline-secondary" disabled title="Não é possível cancelar após o prazo de inscrições">
                        <i class="fas fa-times"></i> Cancelar Inscrição
                    </button>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Inscrição não encontrada.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal de Confirmação de Cancelamento -->
<div class="modal fade" id="modalCancelamento" tabindex="-1" aria-labelledby="modalCancelamentoLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCancelamentoLabel">Confirmar Cancelamento</h5>
                <button type="button" class="btn-close" onclick="fecharModal()" aria-label="Fechar modal">×</button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja cancelar sua inscrição no processo:</p>
                <p><strong id="processoNome"></strong></p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita. Após o cancelamento, você poderá se inscrever novamente apenas se o prazo de inscrições ainda estiver aberto.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Não, manter inscrição</button>
                <form id="formCancelamento" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Sim, cancelar inscrição</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarCancelamento(inscricaoId, processoNome) {
    document.getElementById('processoNome').textContent = processoNome;
    document.getElementById('formCancelamento').action = '/painel/inscricao/cancelar/' + inscricaoId;
    
    const modal = document.getElementById('modalCancelamento');
    modal.classList.add('show');
    modal.style.display = 'flex';
}

function fecharModal() {
    const modal = document.getElementById('modalCancelamento');
    modal.classList.remove('show');
    modal.style.display = 'none';
}

// Fechar modal ao clicar fora dele
document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalCancelamento');
    if (e.target === modal) {
        fecharModal();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModal();
    }
});
</script>
