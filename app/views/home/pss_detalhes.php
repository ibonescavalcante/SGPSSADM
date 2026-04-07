<?php $this->layout("template") ?>

<section class="w-full mx-auto mt-40 bg-white shadow-md rounded-lg p-4 md:p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full flex-1 text-zinc-800 px-2 md:px-0">
        
        <!-- ✅ NOVA SEÇÃO: Exibição de mensagens de erro -->
        <?php if (isset($_GET['erro'])): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium"><?= htmlspecialchars($_GET['erro']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium"><?= htmlspecialchars($_GET['sucesso']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Verificar se deve mostrar modal de período encerrado -->
        <?php if (isset($_GET['modal']) && $_GET['modal'] === 'periodo_encerrado' && isset($_SESSION['modal_periodo_encerrado'])): ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dadosModal = <?= json_encode($_SESSION['modal_periodo_encerrado']) ?>;
                    let dataFormatada = '';
                    let titulo = 'Período de Inscrições Encerrado';
                    
                    if (dadosModal.tipo === 'periodo_encerrado') {
                        dataFormatada = dadosModal.data_fim_formatada || dadosModal.data_fim;
                        titulo = 'Período de Inscrições Encerrado';
                    } else if (dadosModal.tipo === 'periodo_nao_iniciado') {
                        dataFormatada = dadosModal.data_inicio_formatada || dadosModal.data_inicio;
                        titulo = 'Período de Inscrições Não Iniciado';
                    }
                    
                    mostrarModalPeriodoEncerrado(dadosModal.mensagem, dataFormatada, titulo);
                });
            </script>
            <?php unset($_SESSION['modal_periodo_encerrado']); ?>
        <?php endif; ?>
        
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="/" class="hover:text-green-600">Início</a></li>
                <li>/</li>
                <li class="text-gray-800 font-semibold"><?= htmlspecialchars($pss['titulo']) ?></li>
            </ol>
        </nav>

        <!-- Cabeçalho do PSS -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-start gap-6">
                    <!-- Logo/Brasão -->
                    <div class="h-24 w-40 flex items-center justify-center border border-gray-300 rounded shadow-sm">
                        <img src="/assets/brasao.png" alt="Brasão" class="max-h-20">
                    </div>
                    
                    <!-- Informações principais -->
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-800 mb-4">
                            <?= htmlspecialchars($pss['titulo']) ?>
                        </h1>
                        
                        <?php if (!empty($pss['secretaria'])): ?>
                            <p class="text-lg text-gray-600 mb-2">
                                <strong>Secretaria:</strong> <?= htmlspecialchars($pss['secretaria']) ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="flex flex-wrap gap-4 text-sm">
                            <?php if (!empty($pss['ano_exercicio'])): ?>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                                    Ano: <?= $pss['ano_exercicio'] ?>
                                </span>
                            <?php endif; ?>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                <?= count($cargos) ?> Cargo(s) Disponível(is)
                            </span>
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full">
                                Status: <?= ucfirst(str_replace('_', ' ', $pss['status_global'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Ações -->
                    <div class="flex flex-col items-center gap-4">
                        <?php
                        // Configurar timezone brasileiro para comparações
                        $timezone_brasil = new DateTimeZone('America/Sao_Paulo');
                        $agora = new DateTime('now', $timezone_brasil);
                        
                        // Criar objetos DateTime com timezone brasileiro
                        $inicio = new DateTime($pss['inscricao_ini']);
                        $inicio->setTimezone($timezone_brasil);
                        
                        $fim = new DateTime($pss['inscricao_fim']);
                        $fim->setTimezone($timezone_brasil);
                        
                        $periodo_ativo = ($agora >= $inicio && $agora <= $fim);
                        ?>
                        
                        <?php if ($periodo_ativo): ?>
                            <a href="/onboarding/<?= $pss['id'] ?>/zona" 
                               class="inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                                Inscreva-se
                            </a>
                        <?php else: ?>
                            <button onclick="verificarPeriodoInscricao(<?= $pss['id'] ?>, '<?= $pss['inscricao_ini'] ?>', '<?= $pss['inscricao_fim'] ?>')"
                                    class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-gray-400 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow">
                                <?php if ($agora < $inicio): ?>
                                    Inscrições em Breve
                                <?php else: ?>
                                    Inscrições Encerradas
                                <?php endif; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do processo -->
        <?php if (!empty($pss['inscricao_ini']) || !empty($pss['inscricao_fim']) || !empty($pss['descricao'])): ?>
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Informações do Processo</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if (!empty($pss['inscricao_ini'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Início das Inscrições:</label>
                    <?php
                    // Formatar data de início com timezone brasileiro
                    $data_inicio = new DateTime($pss['inscricao_ini']);
                    $data_inicio->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                    ?>
                    <p class="text-gray-800"><?= $data_inicio->format('d/m/Y H:i') ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($pss['inscricao_fim'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fim das Inscrições:</label>
                    <?php
                    // Formatar data de fim com timezone brasileiro
                    $data_fim = new DateTime($pss['inscricao_fim']);
                    $data_fim->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                    ?>
                    <p class="text-gray-800"><?= $data_fim->format('d/m/Y H:i') ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($pss['publicado_em'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Publicado em:</label>
                    <?php
                    // Formatar data de publicação com timezone brasileiro
                    $data_publicacao = new DateTime($pss['publicado_em']);
                    $data_publicacao->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                    ?>
                    <p class="text-gray-800"><?= $data_publicacao->format('d/m/Y') ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($pss['versao'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Versão:</label>
                    <p class="text-gray-800"><?= htmlspecialchars($pss['versao']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($pss['descricao'])): ?>
            <div class="mt-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição:</label>
                <p class="text-gray-800"><?= nl2br(htmlspecialchars($pss['descricao'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Lista de cargos/vagas -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Cargos Disponíveis</h2>
            </div>
            
            <?php if (empty($cargos)): ?>
                <div class="p-6 text-center">
                    <p class="text-gray-600">Nenhum cargo disponível para este processo.</p>
                </div>
            <?php else: ?>
                <?php foreach ($cargos as $cargo): ?>
                    <div class="p-4 md:p-6 border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
                            <!-- Informações do cargo -->
                            <div class="flex flex-col gap-3 w-full lg:flex-1 min-w-0">
                                <h3 class="font-bold text-lg text-gray-800 break-words">
                                    <?= htmlspecialchars($cargo['nome']) ?>
                                </h3>
                                
                                <div class="flex flex-wrap gap-2 text-xs md:text-sm text-gray-600">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        Zona: <?= ucfirst($cargo['zona']) ?>
                                    </span>
                                    <?php if (!empty($cargo['microrregiao'])): ?>
                                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded">
                                            Microrregião: <?= ucfirst($cargo['microrregiao']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                        <?= $cargo['vagas_total'] ?> Vagas
                                    </span>
                                    <?php if ($cargo['vagas_pcd'] > 0): ?>
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">
                                            <?= $cargo['vagas_pcd'] ?> PCD
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($cargo['vagas_ppp'] > 0): ?>
                                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">
                                            <?= $cargo['vagas_ppp'] ?> PPP
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($cargo['cadastro_reserva']): ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
                                            Cadastro Reserva
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($cargo['salario_base'])): ?>
                                    <p class="text-sm text-gray-700">
                                        <strong>Salário:</strong> R$ <?= number_format($cargo['salario_base'], 2, ',', '.') ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo['carga_horaria'])): ?>
                                    <p class="text-sm text-gray-700">
                                        <strong>Carga Horária:</strong> <?= htmlspecialchars($cargo['carga_horaria']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo['secretaria'])): ?>
                                    <p class="text-sm text-gray-700">
                                        <strong>Secretaria:</strong> <?= htmlspecialchars($cargo['secretaria']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo['nivel'])): ?>
                                    <p class="text-sm text-gray-700">
                                        <strong>Nível:</strong> <?= htmlspecialchars($cargo['nivel']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo['requisitos_texto'])): ?>
                                    <div class="text-sm text-gray-700">
                                        <strong>Requisitos:</strong>
                                        <p class="mt-1"><?= nl2br(htmlspecialchars($cargo['requisitos_texto'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Botão de ação -->
                            <div class="flex flex-col items-center gap-2 lg:w-auto w-full">
                                <?php if ($periodo_ativo): ?>
                                    <a href="/pss/<?= $pss['id'] ?>/cargo/<?= $cargo['id'] ?>/inscricao" 
                                       class="w-full lg:w-auto inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-4 py-2 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors text-center">
                                        Inscrever-se
                                    </a>
                                <?php else: ?>
                                    <button onclick="verificarPeriodoInscricao(<?= $pss['id'] ?>, '<?= $pss['inscricao_ini'] ?>', '<?= $pss['inscricao_fim'] ?>')"
                                            class="w-full lg:w-auto inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-gray-400 px-4 py-2 text-sm font-bold uppercase text-white no-underline shadow text-center">
                                        <?php if ($agora < $inicio): ?>
                                            Em Breve
                                        <?php else: ?>
                                            Encerrado
                                        <?php endif; ?>
                                    </button>
                                <?php endif; ?>
                                
                                <a href="/pss/<?= $pss['id'] ?>/cargo/<?= $cargo['id'] ?>" 
                                   class="w-full lg:w-auto text-center text-sm text-blue-600 hover:text-blue-800 underline">
                                    Ver detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Documentos e anexos -->
        <?php if (!empty($pss['edital_url']) || !empty($pss['cronograma_url']) || !empty($pss['anexos'])): ?>
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Documentos</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php if (!empty($pss['edital_url'])): ?>
                    <a href="<?= htmlspecialchars($pss['edital_url']) ?>" target="_blank" 
                       class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-800">Edital</p>
                            <p class="text-sm text-gray-600">Documento oficial</p>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($pss['cronograma_url'])): ?>
                    <a href="<?= htmlspecialchars($pss['cronograma_url']) ?>" target="_blank" 
                       class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-800">Cronograma</p>
                            <p class="text-sm text-gray-600">Datas importantes</p>
                        </div>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($pss['anexos'])): ?>
                    <?php 
                    $anexos = json_decode($pss['anexos'], true);
                    if (is_array($anexos)):
                        foreach ($anexos as $anexo):
                    ?>
                        <a href="<?= htmlspecialchars($anexo['url']) ?>" target="_blank" 
                           class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($anexo['nome']) ?></p>
                                <p class="text-sm text-gray-600">Anexo</p>
                            </div>
                        </a>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Modal de período encerrado -->
<div id="modal-periodo-encerrado" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="modal-titulo">Período Encerrado</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="modal-mensagem">
                    O período de inscrições foi encerrado.
                </p>
                <p class="text-sm text-gray-700 mt-2" id="modal-data">
                    Data: <span id="modal-data-valor"></span>
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="modal-fechar" class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function verificarPeriodoInscricao(pssId, dataInicio, dataFim) {
    // Fazer requisição AJAX para verificar o período
    fetch(`/pss/${pssId}/cargo/1/inscricao`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.valido) {
            mostrarModalPeriodoEncerrado(data.mensagem, data.data_fim_formatada || data.data_inicio_formatada, data.tipo === 'periodo_nao_iniciado' ? 'Período Não Iniciado' : 'Período Encerrado');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        // Fallback para mostrar modal básico
        const agora = new Date();
        const inicio = new Date(dataInicio);
        const fim = new Date(dataFim);
        
        if (agora < inicio) {
            mostrarModalPeriodoEncerrado('O período de inscrições ainda não iniciou.', inicio.toLocaleString('pt-BR'), 'Período Não Iniciado');
        } else {
            mostrarModalPeriodoEncerrado('O período de inscrições foi encerrado.', fim.toLocaleString('pt-BR'), 'Período Encerrado');
        }
    });
}

function mostrarModalPeriodoEncerrado(mensagem, data, titulo = 'Período Encerrado') {
    document.getElementById('modal-titulo').textContent = titulo;
    document.getElementById('modal-mensagem').textContent = mensagem;
    document.getElementById('modal-data-valor').textContent = data;
    document.getElementById('modal-periodo-encerrado').classList.remove('hidden');
}

// Fechar modal
document.getElementById('modal-fechar').addEventListener('click', function() {
    document.getElementById('modal-periodo-encerrado').classList.add('hidden');
});

// Fechar modal clicando fora
document.getElementById('modal-periodo-encerrado').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
