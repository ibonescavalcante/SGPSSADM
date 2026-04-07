<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro novo Processo Seletivo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a73e8;
            --secondary: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary);
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            padding: 15px 20px;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .sidebar {
            background-color: white;
            min-height: calc(100vh - 56px);
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-left: 3px solid transparent;
        }

        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
            border-left: 3px solid var(--primary);
        }

        .sidebar .nav-link.active {
            background-color: #e8f0fe;
            color: var(--primary);
            border-left: 3px solid var(--primary);
        }

        .form-section {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eaeaea;
        }

        .section-title {
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eaeaea;
        }

        .vaga-item {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
        }

        .etapa-item {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid var(--info);
        }

        .remove-btn {
            color: var(--danger);
            cursor: pointer;
        }

        .add-btn {
            color: var(--success);
            cursor: pointer;
        }

        .requisito-label {
            font-weight: 500;
            color: #555;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>Sistema PSS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> Administrador
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Meu Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configurações</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 p-0 sidebar d-none d-md-block">
                <div class="p-3">
                    <h5 class="text-uppercase text-muted small fw-bold">Navegação</h5>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link " href="/dashboard">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard/processos">
                            <i class="fas fa-file-alt me-2"></i> Processos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/candidatos">
                            <i class="fas fa-users me-2"></i> Candidatos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/inscricoes">
                            <i class="fas fa-clipboard-list me-2"></i> Inscrições
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/relatorios">
                            <i class="fas fa-chart-bar me-2"></i> Relatórios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/configuracoes">
                            <i class="fas fa-cog me-2"></i> Configurações
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Conteúdo Principal -->
            <div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0">Cadastro de Processo Seletivo</h2>
                    <div>
                        <button class="btn btn-outline-secondary me-2">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </button>
                        <button class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Salvar Processo
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="processoForm">
                            <!-- Informações Básicas -->
                            <div class="form-section">
                                <h4 class="section-title">Informações Básicas</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="numeroEdital" class="form-label">Número do Edital *</label>
                                        <input type="text" class="form-control" id="numeroEdital"
                                            placeholder="Ex: 001/2023" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="secretaria" class="form-label">Secretaria/Órgão *</label>
                                        <select class="form-select" id="secretaria" required>
                                            <option value="">Selecione uma secretaria</option>
                                            <option value="educacao">Secretaria de Educação</option>
                                            <option value="saude">Secretaria de Saúde</option>
                                            <option value="administracao">Secretaria de Administração</option>
                                            <option value="infraestrutura">Secretaria de Infraestrutura</option>
                                            <option value="assistencia-social">Secretaria de Assistência Social</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="tituloProcesso" class="form-label">Título do Processo Seletivo
                                            *</label>
                                        <input type="text" class="form-control" id="tituloProcesso"
                                            placeholder="Ex: Processo Seletivo para Professores" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="dataInicio" class="form-label">Data de Início das Inscrições
                                            *</label>
                                        <input type="date" class="form-control" id="dataInicio" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="dataFim" class="form-label">Data de Término das Inscrições *</label>
                                        <input type="date" class="form-control" id="dataFim" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="descricao" class="form-label">Descrição do Processo</label>
                                        <textarea class="form-control" id="descricao" rows="3"
                                            placeholder="Descreva o objetivo e características deste processo seletivo"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Vagas e Cargos -->
                            <div class="form-section">
                                <h4 class="section-title">Vagas e Cargos</h4>
                                <div id="vagasContainer">
                                    <div class="vaga-item">
                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label class="form-label">Cargo *</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Ex: Professor de Matemática" required>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Vagas *</label>
                                                <input type="number" class="form-control" min="1" value="1" required>
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Vagas PCD</label>
                                                <input type="number" class="form-control" min="0" value="0">
                                            </div>
                                            <div class="col-md-2 mb-3">
                                                <label class="form-label">Vagas PPP</label>
                                                <input type="number" class="form-control" min="0" value="0">
                                            </div>
                                            <div class="col-md-1 mb-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-danger remove-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Escolaridade Mínima *</label>
                                                <select class="form-select" required>
                                                    <option value="">Selecione</option>
                                                    <option value="fundamental">Ensino Fundamental</option>
                                                    <option value="medio">Ensino Médio</option>
                                                    <option value="tecnico">Ensino Técnico</option>
                                                    <option value="superior">Ensino Superior</option>
                                                    <option value="pos">Pós-Graduação</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Salário (R$)</label>
                                                <input type="number" class="form-control" placeholder="Ex: 2500.00"
                                                    step="0.01">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Requisitos Específicos</label>
                                                <textarea class="form-control" rows="2"
                                                    placeholder="Descreva requisitos específicos para este cargo"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-2" id="addVaga">
                                    <i class="fas fa-plus me-2"></i> Adicionar Cargo
                                </button>
                            </div>

                            <!-- Etapas do Processo -->
                            <div class="form-section">
                                <h4 class="section-title">Etapas do Processo Seletivo</h4>
                                <div id="etapasContainer">
                                    <div class="etapa-item">
                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label class="form-label">Tipo de Etapa *</label>
                                                <select class="form-select" required>
                                                    <option value="">Selecione</option>
                                                    <option value="analise">Análise Documental</option>
                                                    <option value="prova">Prova Objetiva</option>
                                                    <option value="discursiva">Prova Discursiva</option>
                                                    <option value="pratica">Prova Prática</option>
                                                    <option value="entrevista">Entrevista</option>
                                                    <option value="homologacao">Homologação</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5 mb-3">
                                                <label class="form-label">Data Prevista *</label>
                                                <input type="date" class="form-control" required>
                                            </div>
                                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                                <button type="button" class="btn btn-sm btn-danger remove-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Descrição da Etapa</label>
                                                <textarea class="form-control" rows="2"
                                                    placeholder="Descreva os detalhes desta etapa"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-2" id="addEtapa">
                                    <i class="fas fa-plus me-2"></i> Adicionar Etapa
                                </button>
                            </div>

                            <!-- Documentos Obrigatórios -->
                            <div class="form-section">
                                <h4 class="section-title">Documentos Obrigatórios</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docRG" checked>
                                            <label class="form-check-label requisito-label" for="docRG">
                                                Cópia do Documento de Identidade (RG)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docCPF" checked>
                                            <label class="form-check-label requisito-label" for="docCPF">
                                                Cópia do CPF
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docReservista">
                                            <label class="form-check-label requisito-label" for="docReservista">
                                                Certificado de Reservista (se aplicável)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docTitulo">
                                            <label class="form-check-label requisito-label" for="docTitulo">
                                                Cópia do Título de Eleitor
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docEscolaridade"
                                                checked>
                                            <label class="form-check-label requisito-label" for="docEscolaridade">
                                                Comprovante de Escolaridade
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docResidencia">
                                            <label class="form-check-label requisito-label" for="docResidencia">
                                                Comprovante de Residência
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docCurriculo">
                                            <label class="form-check-label requisito-label" for="docCurriculo">
                                                Curriculum Vitae
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="docCertificacoes">
                                            <label class="form-check-label requisito-label" for="docCertificacoes">
                                                Certificados de Cursos
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configurações Adicionais -->
                            <div class="form-section">
                                <h4 class="section-title">Configurações Adicionais</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="taxaInscricao" class="form-label">Taxa de Inscrição (R$)</label>
                                        <input type="number" class="form-control" id="taxaInscricao" placeholder="0.00"
                                            step="0.01" min="0">
                                        <div class="form-text">Deixe em branco ou zero para inscrição gratuita</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="statusProcesso" class="form-label">Status do Processo *</label>
                                        <select class="form-select" id="statusProcesso" required>
                                            <option value="rascunho">Rascunho</option>
                                            <option value="publicado">Publicado</option>
                                            <option value="andamento">Em Andamento</option>
                                            <option value="encerrado">Encerrado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permitePCD">
                                            <label class="form-check-label requisito-label" for="permitePCD">
                                                Permitir inscrição de Pessoas com Deficiência (PCD)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="permitePPP">
                                            <label class="form-check-label requisito-label" for="permitePPP">
                                                Permitir inscrição de Pretos, Pardos e Povos Indígenas (PPP)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-outline-secondary me-3">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Salvar Processo Seletivo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adicionar nova vaga
            document.getElementById('addVaga').addEventListener('click', function() {
                const vagasContainer = document.getElementById('vagasContainer');
                const newVaga = document.createElement('div');
                newVaga.className = 'vaga-item';
                newVaga.innerHTML = `
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Cargo *</label>
                            <input type="text" class="form-control" placeholder="Ex: Professor de Matemática" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Vagas *</label>
                            <input type="number" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Vagas PCD</label>
                            <input type="number" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Vagas PPP</label>
                            <input type="number" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-1 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger remove-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Escolaridade Mínima *</label>
                            <select class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="fundamental">Ensino Fundamental</option>
                                <option value="medio">Ensino Médio</option>
                                <option value="tecnico">Ensino Técnico</option>
                                <option value="superior">Ensino Superior</option>
                                <option value="pos">Pós-Graduação</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Salário (R$)</label>
                            <input type="number" class="form-control" placeholder="Ex: 2500.00" step="0.01">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Requisitos Específicos</label>
                            <textarea class="form-control" rows="2" placeholder="Descreva requisitos específicos para este cargo"></textarea>
                        </div>
                    </div>
                `;
                vagasContainer.appendChild(newVaga);

                // Adicionar evento de remoção
                newVaga.querySelector('.remove-btn').addEventListener('click', function() {
                    if (vagasContainer.children.length > 1) {
                        vagasContainer.removeChild(newVaga);
                    } else {
                        alert('É necessário ter pelo menos um cargo no processo seletivo.');
                    }
                });
            });

            // Adicionar nova etapa
            document.getElementById('addEtapa').addEventListener('click', function() {
                const etapasContainer = document.getElementById('etapasContainer');
                const newEtapa = document.createElement('div');
                newEtapa.className = 'etapa-item';
                newEtapa.innerHTML = `
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Tipo de Etapa *</label>
                            <select class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="analise">Análise Documental</option>
                                <option value="prova">Prova Objetiva</option>
                                <option value="discursiva">Prova Discursiva</option>
                                <option value="pratica">Prova Prática</option>
                                <option value="entrevista">Entrevista</option>
                                <option value="homologacao">Homologação</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label">Data Prevista *</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <button type="button" class="btn btn-sm btn-danger remove-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Descrição da Etapa</label>
                            <textarea class="form-control" rows="2" placeholder="Descreva os detalhes desta etapa"></textarea>
                        </div>
                    </div>
                `;
                etapasContainer.appendChild(newEtapa);

                // Adicionar evento de remoção
                newEtapa.querySelector('.remove-btn').addEventListener('click', function() {
                    if (etapasContainer.children.length > 1) {
                        etapasContainer.removeChild(newEtapa);
                    } else {
                        alert('É necessário ter pelo menos uma etapa no processo seletivo.');
                    }
                });
            });

            // Adicionar eventos de remoção aos elementos iniciais
            document.querySelectorAll('.remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const container = this.closest('.vaga-item') || this.closest('.etapa-item');
                    const parent = container.parentElement;
                    if (parent.children.length > 1) {
                        parent.removeChild(container);
                    } else {
                        alert('É necessário ter pelo menos um item.');
                    }
                });
            });

            // Validação do formulário
            document.getElementById('processoForm').addEventListener('submit', function(e) {
                e.preventDefault();

                // Validação básica
                const numeroEdital = document.getElementById('numeroEdital').value;
                const secretaria = document.getElementById('secretaria').value;
                const tituloProcesso = document.getElementById('tituloProcesso').value;

                if (!numeroEdital || !secretaria || !tituloProcesso) {
                    alert('Por favor, preencha todos os campos obrigatórios.');
                    return;
                }

                // Simulação de sucesso
                alert('Processo seletivo salvo com sucesso!');
                // Aqui você normalmente redirecionaria ou faria uma requisição AJAX
            });

            // Validação de datas
            const dataInicio = document.getElementById('dataInicio');
            const dataFim = document.getElementById('dataFim');

            dataInicio.addEventListener('change', function() {
                if (dataFim.value && new Date(dataFim.value) < new Date(this.value)) {
                    alert('A data de término deve ser posterior à data de início.');
                    this.value = '';
                }
            });

            dataFim.addEventListener('change', function() {
                if (dataInicio.value && new Date(this.value) < new Date(dataInicio.value)) {
                    alert('A data de término deve ser posterior à data de início.');
                    this.value = '';
                }
            });
        });
    </script>
</body>

</html>