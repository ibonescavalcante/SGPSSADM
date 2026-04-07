<?php $this->layout('/painel/painel-template') ?>

<?php
// Helpers de status (normalização + mapeamento)
function normaliza_status($s) {
    $s = trim(strtolower((string)$s));
    // uniformiza separadores
    $s = str_replace(['-', ' '], '_', $s);
    // corrige acento se vier “em análise”
    $s = str_replace(['analise', 'análise'], 'analise', $s);
    return $s;
}

function badge_css_por_status($s) {
    switch (normaliza_status($s)) {
        case 'aberto':     return 'bg-blue-100 text-blue-800';
        case 'em_analise': return 'bg-yellow-100 text-yellow-800';
        case 'deferido':   return 'bg-green-100 text-green-800';
        case 'indeferido': return 'bg-red-100 text-red-800';
        default:           return 'bg-gray-100 text-gray-800';
    }
}

function rotulo_por_status($s) {
    switch (normaliza_status($s)) {
        case 'aberto':     return 'Aberto';
        case 'em_analise': return 'Em Análise';
        case 'deferido':   return 'Deferido';
        case 'indeferido': return 'Indeferido';
        default:           return ucfirst((string)$s);
    }
}
?>

<div class="bg-gray-100 flex-1 flex flex-col items-center text-zinc-800">
    <div class="bg-white min-h-[46rem] w-[54vw] m-8 shadow-md rounded-b-2xl">
        <h2 class="text-3xl uppercase py-8 px-5 border-b border-b-gray-300 border-t-[6px] border-t-green-900 shadow-sm">
            Área do Candidato - <span class="font-bold">Recursos</span>
        </h2>

        <div class="p-6">
            <!-- Mensagens de feedback -->
            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-check-circle mr-2"></i>
                        </div>
                        <div>
                            <p class="font-bold">Sucesso!</p>
                            <p class="text-sm"><?= htmlspecialchars($_GET['success']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['erro'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                        </div>
                        <div>
                            <p class="font-bold">Erro!</p>
                            <p class="text-sm"><?= htmlspecialchars($_GET['erro']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Etapas disponíveis para recurso -->
            <?php if (!empty($etapas_recurso)): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        Recursos Disponíveis
                    </h3>
                    
                    <div class="grid gap-4">
                        <?php foreach ($etapas_recurso as $etapa): ?>
                            <div class="border border-gray-200 rounded-lg p-4 bg-green-50 hover:bg-green-100 transition-colors">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($etapa['etapa_nome']) ?></h4>
                                        <p class="text-sm text-gray-600">
                                            PSS: <?= htmlspecialchars($etapa['pss_nome']) ?> 
                                            | Protocolo: <?= htmlspecialchars($etapa['inscricao_protocolo']) ?>
                                        </p>
                                    </div>
                                    <span class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-medium">
                                        Disponível
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Prazo: <?= date('d/m/Y H:i', strtotime($etapa['rec_ini'])) ?> até 
                                    <?= date('d/m/Y H:i', strtotime($etapa['rec_fim'])) ?>
                                </div>
                                
                                <button onclick="abrirModalRecurso(<?= $etapa['inscricao_id'] ?>, <?= $etapa['etapa_id'] ?>, '<?= htmlspecialchars($etapa['etapa_nome']) ?>', '<?= htmlspecialchars($etapa['pss_nome']) ?>')" 
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-plus mr-1"></i>
                                    Enviar Recurso
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recursos já enviados -->
            <?php if (!empty($recursos_enviados)): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-file-alt text-blue-600 mr-2"></i>
                        Recursos Enviados
                    </h3>
                    
                    <div class="grid gap-4">
                        <?php foreach ($recursos_enviados as $recurso): ?>
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($recurso['etapa_nome']) ?></h4>
                                        <p class="text-sm text-gray-600">
                                            PSS: <?= htmlspecialchars($recurso['pss_nome']) ?> 
                                            | Protocolo: <?= htmlspecialchars($recurso['inscricao_protocolo']) ?>
                                        </p>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium <?= badge_css_por_status($recurso['status'] ?? '') ?>">
                                        <?= rotulo_por_status($recurso['status'] ?? '') ?>
                                    </span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-3">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Enviado em: <?= date("d/m/Y H:i", strtotime($recurso["dt_abertura"])) ?>
                                </div>

                                <?php if (normaliza_status($recurso["status"] ?? '') === "aberto"): ?>
                                    <button onclick="abrirModalResposta(<?= $recurso['id'] ?>, '<?= htmlspecialchars($recurso['etapa_nome']) ?>', '<?= htmlspecialchars($recurso['pss_nome']) ?>', '<?= htmlspecialchars($recurso['motivo_abertura']) ?>', '<?= htmlspecialchars($recurso['inscricao_protocolo']) ?>', '<?= htmlspecialchars($recurso['cargo_nome']) ?>')" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-edit mr-1"></i>
                                        Responder Recurso
                                    </button>
                                <?php else: ?>
                                    <button onclick="visualizarRecurso(<?= $recurso['id'] ?>, '<?= htmlspecialchars($recurso['etapa_nome']) ?>', '<?= htmlspecialchars($recurso['pss_nome']) ?>', '<?= htmlspecialchars($recurso['motivo_abertura']) ?>', '<?= htmlspecialchars($recurso['inscricao_protocolo']) ?>', '<?= htmlspecialchars($recurso['cargo_nome']) ?>', '<?= htmlspecialchars($recurso['resposta_candidato'] ?? '') ?>')" 
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-eye mr-1"></i>
                                        Visualizar
                                    </button>
                                <?php endif; ?>
                                
                                <div class="bg-white p-3 rounded border text-sm mt-3">
                                    <strong>Conteúdo do recurso:</strong>
                                    <p class="mt-1 text-gray-700"><?= nl2br(htmlspecialchars($recurso['conteudo'])) ?></p>
                                </div>
                                
                                <?php if (!empty($recurso['motivo_abertura'])): ?>
                                    <div class="bg-red-50 p-3 rounded border border-red-200 text-sm mt-3">
                                        <strong class="text-red-800">Motivo da reprovação:</strong>
                                        <p class="mt-1 text-red-700"><?= nl2br(htmlspecialchars($recurso['motivo_abertura'])) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($recurso['status'] !== 'aberto' && !empty($recurso['motivo_decisao'])): ?>
                                    <div class="bg-blue-50 p-3 rounded border border-blue-200 text-sm mt-3">
                                        <strong class="text-blue-800">Resposta da avaliação:</strong>
                                        <p class="mt-1 text-blue-700"><?= nl2br(htmlspecialchars($recurso['motivo_decisao'])) ?></p>
                                        <?php if ($recurso['dt_decisao']): ?>
                                            <p class="text-xs text-blue-600 mt-2">
                                                <i class="fas fa-clock mr-1"></i>
                                                Respondido em: <?= date('d/m/Y H:i', strtotime($recurso['dt_decisao'])) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Mensagem quando não há recursos -->
            <?php if (empty($etapas_recurso) && empty($recursos_enviados)): ?>
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-inbox text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum recurso disponível</h3>
                    <p class="text-gray-500">
                        Não há períodos de recurso abertos no momento ou você não possui inscrições elegíveis.
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal para responder recurso -->
<div id="modalResposta" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="modalRespostaTitle">Responder Recurso</h3>
                <button onclick="fecharModalResposta()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="formResposta">
                <input type="hidden" id="recurso_id_resposta" name="recurso_id">
                
                <div class="mb-4">
                    <div class="bg-gray-100 p-4 rounded-lg border">
                        <p class="text-sm text-gray-800">
                            <strong>Etapa:</strong> <span id="modalRespostaEtapaNome"></span><br>
                            <strong>PSS:</strong> <span id="modalRespostaPssNome"></span><br>
                            <strong>Protocolo:</strong> <span id="modalRespostaProtocolo"></span><br>
                            <strong>Cargo:</strong> <span id="modalRespostaCargo"></span>
                        </p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="bg-red-50 p-3 rounded-lg border border-red-200 text-sm">
                        <strong class="text-red-800">Motivo da reprovação:</strong>
                        <p class="mt-1 text-red-700" id="modalRespostaMotivo"></p>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="resposta_conteudo" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-edit mr-1"></i>
                        Sua Resposta *
                    </label>
                    <textarea id="resposta_conteudo" 
                              name="resposta" 
                              rows="8" 
                              maxlength="2000"
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                              placeholder="Descreva detalhadamente os motivos do seu recurso..."></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">Máximo de 2.000 caracteres</p>
                        <span id="contadorCaracteresResposta" class="text-xs text-gray-500">0/2000</span>
                    </div>
                    <div id="alertaCaracteresResposta" class="text-xs mt-1 hidden">
                        <span class="text-orange-600">Atenção: Você está próximo do limite de caracteres!</span>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 mb-4">
                    <p class="text-sm text-yellow-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Importante:</strong> Após enviar sua resposta, não será possível editá-la. 
                        Certifique-se de que todas as informações estão corretas.
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="fecharModalResposta()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            id="btnEnviarResposta"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-paper-plane mr-1"></i>
                        Enviar Resposta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para visualizar recurso (somente leitura) -->
<div id="modalVisualizacao" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Visualizar Recurso</h3>
                <button onclick="fecharModalVisualizacao()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <div class="bg-gray-100 p-4 rounded-lg border">
                    <p class="text-sm text-gray-800">
                        <strong>Etapa:</strong> <span id="modalVisualizacaoEtapaNome"></span><br>
                        <strong>PSS:</strong> <span id="modalVisualizacaoPssNome"></span><br>
                        <strong>Protocolo:</strong> <span id="modalVisualizacaoProtocolo"></span><br>
                        <strong>Cargo:</strong> <span id="modalVisualizacaoCargo"></span>
                    </p>
                </div>
            </div>

            <div class="mb-4">
                <div class="bg-red-50 p-3 rounded-lg border border-red-200 text-sm">
                    <strong class="text-red-800">Motivo da reprovação:</strong>
                    <p class="mt-1 text-red-700" id="modalVisualizacaoMotivo"></p>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-file-text mr-1"></i>
                    Sua Resposta
                </label>
                <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 min-h-[200px]">
                    <p id="modalVisualizacaoResposta" class="text-gray-700 whitespace-pre-wrap"></p>
                </div>
            </div>
            
            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200 mb-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Status:</strong> Este recurso já foi enviado e não pode mais ser editado.
                </p>
            </div>
            
            <div class="flex justify-end">
                <button type="button" 
                        onclick="fecharModalVisualizacao()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Variáveis globais
let modalResposta = null;
let modalVisualizacao = null;

document.addEventListener('DOMContentLoaded', function() {
    modalResposta = document.getElementById('modalResposta');
    modalVisualizacao = document.getElementById('modalVisualizacao');
    
    // Configurar contador de caracteres para resposta
    const respostaConteudo = document.getElementById('resposta_conteudo');
    const contadorResposta = document.getElementById('contadorCaracteresResposta');
    const alertaResposta = document.getElementById('alertaCaracteresResposta');
    
    if (respostaConteudo && contadorResposta) {
        respostaConteudo.addEventListener('input', function() {
            const length = this.value.length;
            contadorResposta.textContent = `${length}/2000`;
            
            // Alertas de quantidade de caracteres
            if (length > 1800) {
                contadorResposta.classList.add("text-red-500");
                contadorResposta.classList.remove("text-orange-500", "text-gray-500");
                alertaResposta.classList.remove("hidden");
                alertaResposta.innerHTML = '<span class="text-red-600">Limite de caracteres quase atingido!</span>';
            } else if (length > 1500) {
                contadorResposta.classList.add("text-orange-500");
                contadorResposta.classList.remove("text-red-500", "text-gray-500");
                alertaResposta.classList.remove("hidden");
                alertaResposta.innerHTML = '<span class="text-orange-600">Atenção: Você está próximo do limite de caracteres!</span>';
            } else {
                contadorResposta.classList.add("text-gray-500");
                contadorResposta.classList.remove("text-red-500", "text-orange-500");
                alertaResposta.classList.add("hidden");
            }
        });
    }

    // Configurar formulário de resposta
    const formResposta = document.getElementById('formResposta');
    if (formResposta) {
        formResposta.addEventListener('submit', function(e) {
            e.preventDefault();
            enviarResposta();
        });
    }
});

function abrirModalResposta(recursoId, etapaNome, pssNome, motivo, protocolo, cargo) {
    document.getElementById('recurso_id_resposta').value = recursoId;
    document.getElementById('modalRespostaEtapaNome').textContent = etapaNome;
    document.getElementById('modalRespostaPssNome').textContent = pssNome;
    document.getElementById('modalRespostaMotivo').textContent = motivo;
    document.getElementById('modalRespostaProtocolo').textContent = protocolo;
    document.getElementById('modalRespostaCargo').textContent = cargo;
    
    // Limpar campos
    document.getElementById('resposta_conteudo').value = '';
    document.getElementById('contadorCaracteresResposta').textContent = '0/2000';
    document.getElementById('alertaCaracteresResposta').classList.add('hidden');
    
    // Habilitar botão
    const btnEnviar = document.getElementById('btnEnviarResposta');
    btnEnviar.disabled = false;
    btnEnviar.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Enviar Resposta';
    
    modalResposta.classList.remove('hidden');
}

function fecharModalResposta() {
    modalResposta.classList.add('hidden');
}

function visualizarRecurso(recursoId, etapaNome, pssNome, motivo, protocolo, cargo, resposta) {
    document.getElementById('modalVisualizacaoEtapaNome').textContent = etapaNome;
    document.getElementById('modalVisualizacaoPssNome').textContent = pssNome;
    document.getElementById('modalVisualizacaoMotivo').textContent = motivo;
    document.getElementById('modalVisualizacaoProtocolo').textContent = protocolo;
    document.getElementById('modalVisualizacaoCargo').textContent = cargo;
    document.getElementById('modalVisualizacaoResposta').textContent = resposta || 'Nenhuma resposta fornecida.';
    
    modalVisualizacao.classList.remove('hidden');
}

function fecharModalVisualizacao() {
    modalVisualizacao.classList.add('hidden');
}

function enviarResposta() {
    const recursoId = document.getElementById('recurso_id_resposta').value;
    const resposta = document.getElementById('resposta_conteudo').value.trim();
    const btnEnviar = document.getElementById('btnEnviarResposta');
    
    // Validações
    if (!resposta) {
        alert('Por favor, preencha sua resposta.');
        return;
    }
    
    if (resposta.length < 10) {
        alert('A resposta deve ter pelo menos 10 caracteres.');
        return;
    }
    
    if (resposta.length > 2000) {
        alert('A resposta não pode ter mais de 2.000 caracteres.');
        return;
    }
    
    // Confirmar envio
    if (!confirm('Tem certeza que deseja enviar esta resposta? Após o envio não será possível editá-la e seguirá para avaliação.')) {
        return;
    }
    
    // Desabilitar botão e mostrar loading
    btnEnviar.disabled = true;
    btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Enviando...';
    
    // Enviar via AJAX
    fetch('/painel/responder-recurso', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            recurso_id: recursoId,
            resposta: resposta
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Resposta enviada com sucesso!');
            location.reload(); // Recarregar a página para mostrar as mudanças
        } else {
            alert('Erro: ' + data.message);
            // Reabilitar botão em caso de erro
            btnEnviar.disabled = false;
            btnEnviar.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Enviar Resposta';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro interno do servidor. Tente novamente.');
        // Reabilitar botão em caso de erro
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Enviar Resposta';
    });
}

// Fechar modais ao clicar fora
document.addEventListener('click', function(e) {
    if (e.target === modalResposta) {
        fecharModalResposta();
    }
    if (e.target === modalVisualizacao) {
        fecharModalVisualizacao();
    }
});

// Fechar modais com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalResposta();
        fecharModalVisualizacao();
    }
});
</script>

