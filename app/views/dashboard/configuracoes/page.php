<?php $this->layout('dashboard/template') ?>
<?php
$usuarios = $usuarios ?? [];
$perfis_form = $perfis_form ?? [];
$perfil_labels = $perfil_labels ?? [];
$pode_gerir_usuarios = !empty($pode_gerir_usuarios);
$badgePerfil = static function (string $perfil): string {
    return match ($perfil) {
        'administrador' => 'bg-primary',
        'comissao' => 'bg-info',
        'visualizador' => 'bg-secondary',
        default => 'bg-secondary',
    };
};
?>
<!-- Conteúdo Principal -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Configurações do Sistema</h2>
        <!--button class="btn btn-primary" id="btnSalvarConfiguracoes">
            <i class="fas fa-save me-2"></i> Salvar Alterações
        </button-->
    </div>

    <div class="row">
        <!-- Navegação de Configurações -->
        <div class="col-lg-3 mb-4">
            <div class="card settings-nav">
                <div class="nav flex-column">
                    <!--a class="nav-link active" href="#gerais" data-bs-toggle="tab">
                        <i class="fas fa-cog"></i> Gerais
                    </a>
                    <a class="nav-link" href="#notificacoes" data-bs-toggle="tab">
                        <i class="fas fa-bell"></i> Notificações
                    </a -->
                    <a class="nav-link" href="#usuarios" data-bs-toggle="tab">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                    <!--a class="nav-link" href="#seguranca" data-bs-toggle="tab">
                        <i class="fas fa-shield-alt"></i> Segurança
                    </a>
                    <a class="nav-link" href="#backup" data-bs-toggle="tab">
                        <i class="fas fa-database"></i> Backup
                    </a>
                    <a class="nav-link" href="#api" data-bs-toggle="tab">
                        <i class="fas fa-code"></i> API
                    </a>
                    <a class="nav-link" href="#personalizacao" data-bs-toggle="tab">
                        <i class="fas fa-paint-brush"></i> Personalização
                    </a-->
                </div>
            </div>

            
        </div>

        <!-- Conteúdo das Configurações -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Configurações Gerais -->
                <div class="tab-pane fade show " id="gerais">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Configurações Gerais do Sistema</h5>
                        </div>
                        <div class="card-body">
                            <div class="config-section">
                                <h5 class="section-title">Informações Básicas</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nomeSistema" class="form-label">Nome do Sistema</label>
                                        <input type="text" class="form-control" id="nomeSistema" value="Sistema PSS">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="emailSistema" class="form-label">E-mail do
                                            Sistema</label>
                                        <input type="email" class="form-control" id="emailSistema"
                                            value="contato@sistemapss.com.br">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="telefoneSistema" class="form-label">Telefone de
                                            Contato</label>
                                        <input type="text" class="form-control" id="telefoneSistema"
                                            value="(11) 9999-9999">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="timezone" class="form-label">Fuso Horário</label>
                                        <select class="form-select" id="timezone">
                                            <option value="-3" selected>Brasília (UTC-3)</option>
                                            <option value="-4">Manaus (UTC-4)</option>
                                            <option value="-5">Rio Branco (UTC-5)</option>
                                            <option value="-2">Fernando de Noronha (UTC-2)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h5 class="section-title">Preferências do Sistema</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="idioma" class="form-label">Idioma</label>
                                        <select class="form-select" id="idioma">
                                            <option value="pt-BR" selected>Português (Brasil)</option>
                                            <option value="es">Español</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="paginaInicial" class="form-label">Página Inicial</label>
                                        <select class="form-select" id="paginaInicial">
                                            <option value="dashboard" selected>Dashboard</option>
                                            <option value="processos">Processos</option>
                                            <option value="candidatos">Candidatos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="itensPorPagina" class="form-label">Itens por
                                            Página</label>
                                        <select class="form-select" id="itensPorPagina">
                                            <option value="10">10 itens</option>
                                            <option value="25" selected>25 itens</option>
                                            <option value="50">50 itens</option>
                                            <option value="100">100 itens</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="formatoData" class="form-label">Formato de Data</label>
                                        <select class="form-select" id="formatoData">
                                            <option value="dd/MM/yyyy" selected>DD/MM/AAAA</option>
                                            <option value="MM/dd/yyyy">MM/DD/AAAA</option>
                                            <option value="yyyy-MM-dd">AAAA-MM-DD</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h5 class="section-title">Opções de Processo Seletivo</h5>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="permitirEdicao" checked>
                                        <label class="form-check-label" for="permitirEdicao">Permitir edição
                                            de processos após publicação</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notificarCandidatos"
                                            checked>
                                        <label class="form-check-label" for="notificarCandidatos">Notificar
                                            candidatos automaticamente</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="exibirClassificacao">
                                        <label class="form-check-label" for="exibirClassificacao">Exibir
                                            classificação para candidatos</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações de Notificações -->
                <div class="tab-pane fade" id="notificacoes">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Configurações de Notificações</h5>
                        </div>
                        <div class="card-body">
                            <div class="config-section">
                                <h5 class="section-title">Notificações por E-mail</h5>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emailNotificacoes" checked>
                                        <label class="form-check-label" for="emailNotificacoes">Ativar
                                            notificações por e-mail</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emailInscricoes" checked>
                                        <label class="form-check-label" for="emailInscricoes">Notificar
                                            novas inscrições</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="emailResultados" checked>
                                        <label class="form-check-label" for="emailResultados">Notificar
                                            resultados de processos</label>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h5 class="section-title">Notificações no Sistema</h5>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notifSistema" checked>
                                        <label class="form-check-label" for="notifSistema">Ativar
                                            notificações no sistema</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notifDocumentos" checked>
                                        <label class="form-check-label" for="notifDocumentos">Notificar
                                            documentos pendentes</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notifPrazos" checked>
                                        <label class="form-check-label" for="notifPrazos">Notificar prazos
                                            próximos</label>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h5 class="section-title">Modelos de E-mail</h5>
                                <div class="mb-3">
                                    <label for="modeloInscricao" class="form-label">Modelo de Confirmação de
                                        Inscrição</label>
                                    <textarea class="form-control" id="modeloInscricao"
                                        rows="4">Prezado(a) {candidato_nome},\n\nSua inscrição no processo seletivo {processo_titulo} foi recebida com sucesso.\n\nAcompanhe o andamento pelo sistema.\n\nAtenciosamente,\nEquipe {sistema_nome}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="modeloResultado" class="form-label">Modelo de
                                        Resultado</label>
                                    <textarea class="form-control" id="modeloResultado"
                                        rows="4">Prezado(a) {candidato_nome},\n\nO resultado do processo seletivo {processo_titulo} já está disponível.\n\nAcesse o sistema para conferir.\n\nAtenciosamente,\nEquipe {sistema_nome}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configurações de Usuários -->
                <div class="tab-pane fade" id="usuarios">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Gerenciamento de Usuários</h5>
                            <?php if ($pode_gerir_usuarios): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#modalNovoUsuario">
                                <i class="fas fa-plus me-1"></i> Novo Usuário
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!$pode_gerir_usuarios): ?>
                            <div class="alert alert-info mb-3" role="alert">
                                Apenas utilizadores com perfil <strong>Administrador</strong> podem criar ou editar
                                utilizadores. A lista abaixo é apenas para consulta.
                            </div>
                            <?php endif; ?>
                            <div id="novoUsuarioAlertas" class="mb-3"></div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Usuário</th>
                                            <th>E-mail</th>
                                            <th>Perfil</th>
                                            <th>Status</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabelaUsuariosBody">
                                        <?php if (empty($usuarios)): ?>
                                        <tr>
                                            <td colspan="5" class="text-muted text-center py-4">Nenhum utilizador
                                                cadastrado.<?php if ($pode_gerir_usuarios): ?> Use &quot;Novo
                                                Usuário&quot; para adicionar.<?php endif; ?></td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($usuarios as $u): ?>
                                        <?php
                                            $pid = strtolower(trim((string) ($u['perfil'] ?? '')));
                                            $perfilNome = $perfil_labels[$pid] ?? $pid;
                                            $ativo = !empty($u['ativo']) && ($u['ativo'] === true || $u['ativo'] === 't' || $u['ativo'] === '1' || $u['ativo'] === 1);
                                            ?>
                                        <tr>
                                            <td><?= htmlspecialchars((string) ($u['nome'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td><?= htmlspecialchars((string) ($u['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td><span
                                                    class="badge <?= $badgePerfil($pid) ?> user-role-badge"><?= htmlspecialchars($perfilNome, ENT_QUOTES, 'UTF-8') ?></span>
                                            </td>
                                            <td><?php if ($ativo): ?><span class="badge bg-success">Ativo</span><?php else: ?><span
                                                    class="badge bg-warning">Inativo</span><?php endif; ?></td>
                                            <td>
                                                <?php if ($pode_gerir_usuarios):
                                                    $editPayload = json_encode([
                                                        'id' => (int) ($u['id'] ?? 0),
                                                        'nome' => (string) ($u['nome'] ?? ''),
                                                        'email' => (string) ($u['email'] ?? ''),
                                                        'telefone' => (string) ($u['telefone'] ?? ''),
                                                        'perfil' => $pid,
                                                        'ativo' => $ativo,
                                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
                                                    ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-editar-usuario"
                                                    data-user="<?= htmlspecialchars($editPayload, ENT_QUOTES, 'UTF-8') ?>"
                                                    title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php else: ?>
                                                <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if ($pode_gerir_usuarios): ?>
                            <!-- Modal: novo usuário -->
                            <div class="modal fade" id="modalNovoUsuario" tabindex="-1"
                                aria-labelledby="modalNovoUsuarioLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalNovoUsuarioLabel">Novo usuário</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Fechar"></button>
                                        </div>
                                        <form id="formNovoUsuario" novalidate>
                                            <div class="modal-body">
                                                <div id="formNovoUsuarioErro" class="alert alert-danger d-none"
                                                    role="alert"></div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="nu_nome" class="form-label">Nome completo <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="nu_nome" name="nome"
                                                            required minlength="2" maxlength="200"
                                                            autocomplete="name">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="nu_email" class="form-label">E-mail <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" id="nu_email"
                                                            name="email" required maxlength="180"
                                                            autocomplete="email">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="nu_telefone" class="form-label">Telefone</label>
                                                        <input type="text" class="form-control" id="nu_telefone"
                                                            name="telefone" maxlength="40" placeholder="(00) 00000-0000"
                                                            autocomplete="tel">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="nu_perfil" class="form-label">Perfil <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select" id="nu_perfil" name="perfil"
                                                            required>
                                                            <?php foreach ($perfis_form as $op): ?>
                                                            <option value="<?= htmlspecialchars($op['value'], ENT_QUOTES, 'UTF-8') ?>">
                                                                <?= htmlspecialchars($op['label'], ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="nu_senha" class="form-label">Senha <span
                                                                class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="nu_senha"
                                                            name="senha" required minlength="8" maxlength="128"
                                                            autocomplete="new-password">
                                                        <div class="form-text">Mínimo de 8 caracteres.</div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="nu_senha2" class="form-label">Confirmar senha <span
                                                                class="text-danger">*</span></label>
                                                        <input type="password" class="form-control" id="nu_senha2"
                                                            name="senha_confirmacao" required minlength="8"
                                                            maxlength="128" autocomplete="new-password">
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="nu_ativo" name="ativo" checked>
                                                            <label class="form-check-label" for="nu_ativo">Usuário
                                                                ativo</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary" id="btnSalvarNovoUsuario">
                                                    <i class="fas fa-save me-1"></i> Salvar
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal: editar usuário -->
                            <div class="modal fade" id="modalEditarUsuario" tabindex="-1"
                                aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuário</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Fechar"></button>
                                        </div>
                                        <form id="formEditarUsuario" novalidate>
                                            <div class="modal-body">
                                                <input type="hidden" id="eu_id" name="id" value="">
                                                <div id="formEditarUsuarioErro" class="alert alert-danger d-none"
                                                    role="alert"></div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="eu_nome" class="form-label">Nome completo <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="eu_nome" name="nome"
                                                            required minlength="2" maxlength="200" autocomplete="name">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="eu_email" class="form-label">E-mail <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" id="eu_email" name="email"
                                                            required maxlength="180" autocomplete="email">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="eu_telefone" class="form-label">Telefone</label>
                                                        <input type="text" class="form-control" id="eu_telefone"
                                                            name="telefone" maxlength="40" placeholder="(00) 00000-0000"
                                                            autocomplete="tel">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="eu_perfil" class="form-label">Perfil <span
                                                                class="text-danger">*</span></label>
                                                        <select class="form-select" id="eu_perfil" name="perfil" required>
                                                            <?php foreach ($perfis_form as $op): ?>
                                                            <option value="<?= htmlspecialchars($op['value'], ENT_QUOTES, 'UTF-8') ?>">
                                                                <?= htmlspecialchars($op['label'], ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="eu_senha" class="form-label">Nova senha</label>
                                                        <input type="password" class="form-control" id="eu_senha"
                                                            name="senha" minlength="8" maxlength="128"
                                                            autocomplete="new-password">
                                                        <div class="form-text">Deixe em branco para manter a senha atual.
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="eu_senha2" class="form-label">Confirmar nova senha</label>
                                                        <input type="password" class="form-control" id="eu_senha2"
                                                            name="senha_confirmacao" minlength="8" maxlength="128"
                                                            autocomplete="new-password">
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="eu_ativo"
                                                                name="ativo" checked>
                                                            <label class="form-check-label" for="eu_ativo">Utilizador
                                                                ativo</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-primary" id="btnSalvarEditarUsuario">
                                                    <i class="fas fa-save me-1"></i> Guardar alterações
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="config-section mt-4">
                                <h5 class="section-title">Permissões de Perfis</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">Administrador</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="admin1" checked
                                                        disabled>
                                                    <label class="form-check-label" for="admin1">Acesso
                                                        completo ao sistema</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="admin2" checked
                                                        disabled>
                                                    <label class="form-check-label" for="admin2">Gerenciar
                                                        usuários</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="admin3" checked
                                                        disabled>
                                                    <label class="form-check-label" for="admin3">Configurações
                                                        do sistema</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0">Comissão Avaliadora</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="comissao1"
                                                        checked>
                                                    <label class="form-check-label" for="comissao1">Gerenciar
                                                        processos</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="comissao2"
                                                        checked>
                                                    <label class="form-check-label" for="comissao2">Avaliar
                                                        candidatos</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="comissao3">
                                                    <label class="form-check-label" for="comissao3">Gerar
                                                        relatórios</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outras abas de configurações (Segurança, Backup, API, Personalização) -->
                <div class="tab-pane fade" id="seguranca">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Configurações de Segurança</h5>
                        </div>
                        <div class="card-body">
                            <div class="config-section">
                                <h5 class="section-title">Autenticação</h5>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="autenticacao2fatores">
                                        <label class="form-check-label" for="autenticacao2fatores">Exigir
                                            autenticação de dois fatores</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="bloqueioTentativas" checked>
                                        <label class="form-check-label" for="bloqueioTentativas">Bloquear
                                            conta após tentativas fracassadas</label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="tentativasLogin" class="form-label">Tentativas de login
                                            permitidas</label>
                                        <input type="number" class="form-control" id="tentativasLogin" value="5" min="1"
                                            max="10">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tempoBloqueio" class="form-label">Tempo de bloqueio
                                            (minutos)</label>
                                        <input type="number" class="form-control" id="tempoBloqueio" value="30" min="1"
                                            max="1440">
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h5 class="section-title">Senhas</h5>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="exigirSenhaForte" checked>
                                        <label class="form-check-label" for="exigirSenhaForte">Exigir senha
                                            forte</label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="validadeSenha" class="form-label">Validade da senha
                                            (dias)</label>
                                        <input type="number" class="form-control" id="validadeSenha" value="90" min="1"
                                            max="365">
                                        <div class="form-text">0 para não expirar</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="historicoSenhas" class="form-label">Histórico de
                                            senhas</label>
                                        <input type="number" class="form-control" id="historicoSenhas" value="5" min="0"
                                            max="10">
                                        <div class="form-text">Impedir reutilização das últimas senhas</div>
                                    </div>
                                </div>
                            </div>

                            <div class="config-section">
                                <h5 class="section-title">LGPD e Privacidade</h5>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="lgpdConsentimento" checked>
                                        <label class="form-check-label" for="lgpdConsentimento">Solicitar
                                            consentimento LGPD</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="anonimizacaoDados">
                                        <label class="form-check-label" for="anonimizacaoDados">Anonimizar
                                            dados automaticamente</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="tempoRetencao" class="form-label">Tempo de retenção de dados
                                        (meses)</label>
                                    <input type="number" class="form-control" id="tempoRetencao" value="24" min="1"
                                        max="60">
                                    <div class="form-text">Tempo que os dados serão mantidos após fim do
                                        processo</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo das outras abas seria similar -->
                <div class="tab-pane fade" id="backup">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Backup e Restauração</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Último backup realizado em:
                                15/08/2023 23:45
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card backup-card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-database text-primary fa-3x mb-3"></i>
                                            <h5>Backup do Sistema</h5>
                                            <p class="card-text">Realize backup completo do sistema e dos
                                                dados</p>
                                            <button class="btn btn-primary">
                                                <i class="fas fa-download me-2"></i> Fazer Backup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card backup-card h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-upload text-success fa-3x mb-3"></i>
                                            <h5>Restauração</h5>
                                            <p class="card-text">Restaurar sistema a partir de um backup
                                                anterior</p>
                                            <button class="btn btn-success">
                                                <i class="fas fa-upload me-2"></i> Restaurar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="api">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Configurações de API</h5>
                        </div>
                        <div class="card-body">
                            <div class="config-section">
                                <h5 class="section-title">Chave de API</h5>
                                <div class="mb-3">
                                    <label class="form-label">Chave de API atual</label>
                                    <div class="api-key">ak_7Xg9pL2qR4tW6yZ8xV0bN3mK5jH7fD1c</div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-copy me-1"></i> Copiar
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-sync-alt me-1"></i> Regenerar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="personalizacao">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Personalização do Sistema</h5>
                        </div>
                        <div class="card-body">
                            <div class="config-section">
                                <h5 class="section-title">Aparência</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="temaSistema" class="form-label">Tema do Sistema</label>
                                        <select class="form-select" id="temaSistema">
                                            <option value="claro" selected>Claro</option>
                                            <option value="escuro">Escuro</option>
                                            <option value="auto">Automático</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="corPrimaria" class="form-label">Cor Primária</label>
                                        <input type="color" class="form-control form-control-color" id="corPrimaria"
                                            value="#1a73e8" title="Escolha sua cor">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>
            
        </div>
                                                            <!-- Status do Sistema -->
    <div  style="display: flex; justify-content: center; width: 100%; border: 0px solid #000;">
           <div class="card mt-4" style="width: 30%;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-info-circle text-primary fa-2x"></i>
                    </div>
                    <h6>Status do Sistema</h6>
                    <div class="mb-2">
                        <span class="badge bg-success">Online</span>
                    </div>
                    <small class="text-muted">Versão 2.3.1</small>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i> Verificar Atualizações
                        </button>
                    </div>
                </div>
            </div>
    <div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tabs
        const triggerTabList = document.querySelectorAll('.settings-nav .nav-link');
        triggerTabList.forEach(triggerEl => {
            new bootstrap.Tab(triggerEl);
        });

        // Salvar configurações (botão opcional na UI)
        const btnSalvarCfg = document.getElementById('btnSalvarConfiguracoes');
        if (btnSalvarCfg) {
            btnSalvarCfg.addEventListener('click', function() {
                const toast = document.createElement('div');
                toast.className =
                    'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '1060';
                toast.innerHTML =
                    '<i class="fas fa-check-circle me-2"></i> Configurações salvas com sucesso!' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
        }

        // Alternar entre abas e salvar estado
        triggerTabList.forEach(tab => {
            tab.addEventListener('click', function() {
                localStorage.setItem('ultimaAbaConfig', this.getAttribute('href'));
            });
        });

        // Restaurar última aba acessada
        const ultimaAba = localStorage.getItem('ultimaAbaConfig');
        if (ultimaAba) {
            const tab = document.querySelector(`.settings-nav .nav-link[href="${ultimaAba}"]`);
            if (tab) {
                let inst = bootstrap.Tab.getInstance(tab);
                if (!inst) {
                    inst = new bootstrap.Tab(tab);
                }
                inst.show();
            }
        }

        const formNovo = document.getElementById('formNovoUsuario');
        const erroBox = document.getElementById('formNovoUsuarioErro');
        const btnSalvarNovo = document.getElementById('btnSalvarNovoUsuario');

        function getCsrf() {
            if (typeof getDashboardCsrfToken === 'function') {
                return getDashboardCsrfToken();
            }
            const m = document.querySelector('meta[name="csrf-token"]');
            return m ? (m.getAttribute('content') || '') : '';
        }

        if (formNovo) {
            formNovo.addEventListener('submit', async function(e) {
                e.preventDefault();
                erroBox.classList.add('d-none');
                erroBox.textContent = '';

                const payload = {
                    nome: document.getElementById('nu_nome').value.trim(),
                    email: document.getElementById('nu_email').value.trim(),
                    telefone: document.getElementById('nu_telefone').value.trim(),
                    perfil: document.getElementById('nu_perfil').value,
                    senha: document.getElementById('nu_senha').value,
                    senha_confirmacao: document.getElementById('nu_senha2').value,
                    ativo: document.getElementById('nu_ativo').checked
                };

                btnSalvarNovo.disabled = true;
                try {
                    const res = await fetch('/configuracoes/usuario', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': getCsrf()
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json().catch(function() {
                        return {};
                    });
                    if (!res.ok) {
                        erroBox.textContent = data.erro || 'Não foi possível salvar.';
                        erroBox.classList.remove('d-none');
                        return;
                    }
                    const modalEl = document.getElementById('modalNovoUsuario');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    }
                    window.location.reload();
                } catch (err) {
                    erroBox.textContent = 'Erro de rede. Tente novamente.';
                    erroBox.classList.remove('d-none');
                } finally {
                    btnSalvarNovo.disabled = false;
                }
            });
        }

        const modalNovoEl = document.getElementById('modalNovoUsuario');
        if (modalNovoEl && erroBox) {
            modalNovoEl.addEventListener('hidden.bs.modal', function() {
                const f = document.getElementById('formNovoUsuario');
                if (f) {
                    f.reset();
                }
                const nuAtivo = document.getElementById('nu_ativo');
                if (nuAtivo) {
                    nuAtivo.checked = true;
                }
                erroBox.classList.add('d-none');
                erroBox.textContent = '';
            });
        }

        const formEdit = document.getElementById('formEditarUsuario');
        const erroEdit = document.getElementById('formEditarUsuarioErro');
        const btnSalvarEdit = document.getElementById('btnSalvarEditarUsuario');

        document.querySelectorAll('.btn-editar-usuario').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const raw = this.getAttribute('data-user');
                let u;
                try {
                    u = JSON.parse(raw);
                } catch (e) {
                    return;
                }
                const idEl = document.getElementById('eu_id');
                if (!idEl) {
                    return;
                }
                idEl.value = u.id;
                document.getElementById('eu_nome').value = u.nome || '';
                document.getElementById('eu_email').value = u.email || '';
                document.getElementById('eu_telefone').value = u.telefone || '';
                document.getElementById('eu_perfil').value = u.perfil || 'comissao';
                document.getElementById('eu_ativo').checked = !!u.ativo;
                document.getElementById('eu_senha').value = '';
                document.getElementById('eu_senha2').value = '';
                if (erroEdit) {
                    erroEdit.classList.add('d-none');
                    erroEdit.textContent = '';
                }
                const modalEl = document.getElementById('modalEditarUsuario');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            });
        });

        if (formEdit && erroEdit && btnSalvarEdit) {
            formEdit.addEventListener('submit', async function(e) {
                e.preventDefault();
                erroEdit.classList.add('d-none');
                erroEdit.textContent = '';

                const senhaVal = document.getElementById('eu_senha').value;
                const senha2Val = document.getElementById('eu_senha2').value;
                const payload = {
                    id: parseInt(document.getElementById('eu_id').value, 10) || 0,
                    nome: document.getElementById('eu_nome').value.trim(),
                    email: document.getElementById('eu_email').value.trim(),
                    telefone: document.getElementById('eu_telefone').value.trim(),
                    perfil: document.getElementById('eu_perfil').value,
                    ativo: document.getElementById('eu_ativo').checked,
                    senha: senhaVal,
                    senha_confirmacao: senha2Val
                };

                btnSalvarEdit.disabled = true;
                try {
                    const res = await fetch('/configuracoes/usuario/atualizar', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': getCsrf()
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json().catch(function() {
                        return {};
                    });
                    if (!res.ok) {
                        erroEdit.textContent = data.erro || 'Não foi possível guardar.';
                        erroEdit.classList.remove('d-none');
                        return;
                    }
                    const modalEl = document.getElementById('modalEditarUsuario');
                    const inst = modalEl ? bootstrap.Modal.getInstance(modalEl) : null;
                    if (inst) {
                        inst.hide();
                    }
                    window.location.reload();
                } catch (err) {
                    erroEdit.textContent = 'Erro de rede. Tente novamente.';
                    erroEdit.classList.remove('d-none');
                } finally {
                    btnSalvarEdit.disabled = false;
                }
            });
        }

        const modalEditEl = document.getElementById('modalEditarUsuario');
        if (modalEditEl && erroEdit) {
            modalEditEl.addEventListener('hidden.bs.modal', function() {
                const f = document.getElementById('formEditarUsuario');
                if (f) {
                    f.reset();
                }
                const euAtivo = document.getElementById('eu_ativo');
                if (euAtivo) {
                    euAtivo.checked = true;
                }
                erroEdit.classList.add('d-none');
                erroEdit.textContent = '';
            });
        }
    });
</script>