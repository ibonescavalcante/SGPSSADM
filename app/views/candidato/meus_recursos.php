<?php $this->layout('painel/painel-template') ?>

<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-file-alt text-green-600 mr-2"></i>
            ÁREA DO CANDIDATO - MEUS RECURSOS
        </h1>

        <!-- Resumo dos recursos (movido para o topo) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-center">
                <div class="text-2xl font-bold text-blue-600"><?= count($recursos) ?></div>
                <div class="text-sm text-blue-800">Total</div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 text-center">
                <div class="text-2xl font-bold text-yellow-600">
                    <?= count(array_filter($recursos, fn($r) => $r['status'] === 'em_analise')) ?>
                </div>
                <div class="text-sm text-yellow-800">Em análise</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
                <div class="text-2xl font-bold text-green-600">
                    <?= count(array_filter($recursos, fn($r) => $r['status'] === 'deferido')) ?>
                </div>
                <div class="text-sm text-green-800">Deferidos</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200 text-center">
                <div class="text-2xl font-bold text-red-600">
                    <?= count(array_filter($recursos, fn($r) => $r['status'] === 'indeferido')) ?>
                </div>
                <div class="text-sm text-red-800">Indeferidos</div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="flex flex-wrap gap-4 mb-6">
            <div class="flex items-center gap-2">
                <label for="filtro-status" class="text-sm font-medium text-gray-700">Filtrar por status:</label>
                <select id="filtro-status" class="border border-gray-300 rounded px-3 py-1 text-sm">
                    <option value="">Todos</option>
                    <option value="aberto">Aguardando resposta</option>
                    <option value="em_analise">Em análise</option>
                    <option value="deferido">Deferidos</option>
                    <option value="indeferido">Indeferidos</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label for="busca" class="text-sm font-medium text-gray-700">Buscar:</label>
                <input type="text" id="busca" placeholder="PSS, etapa ou protocolo..." 
                       class="border border-gray-300 rounded px-3 py-1 text-sm w-64">
            </div>
        </div>

        <!-- Lista de recursos -->
        <div class="space-y-4" id="lista-recursos">
            <?php if (empty($recursos)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4"></i>
                    <p>Você não possui recursos cadastrados.</p>
                </div>
            <?php else: ?>
                <?php foreach ($recursos as $recurso): ?>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer recurso-item" 
                         data-status="<?= $recurso['status'] ?>"
                         data-pss="<?= strtolower($recurso['pss_titulo'] ?? '') ?>"
                         data-etapa="<?= strtolower($recurso['etapa_nome'] ?? '') ?>"
                         data-protocolo="<?= strtolower($recurso['protocolo'] ?? '') ?>"
                         onclick="abrirModalRecurso(<?= htmlspecialchars(json_encode($recurso)) ?>)">
                        
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-800 mb-2">
                                    <?= htmlspecialchars($recurso['pss_titulo'] ?? 'PSS não informado') ?>
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm text-gray-600 mb-3">
                                    <div><strong>Etapa:</strong> <?= htmlspecialchars($recurso['etapa_nome'] ?? 'Não informado') ?></div>
                                    <div><strong>Cargo:</strong> <?= htmlspecialchars($recurso['cargo_nome'] ?? 'Não informado') ?></div>
                                    <div><strong>Protocolo:</strong> <?= htmlspecialchars($recurso['protocolo'] ?? 'Não informado') ?></div>
                                </div>
                                
                                <div class="text-sm text-gray-500">
                                    <strong>Enviado em:</strong> <?= date('d/m/Y H:i', strtotime($recurso['dt_abertura'])) ?>
                                </div>
                            </div>
                            
                            <div class="ml-4">
                                <?php
                                $statusClass = match($recurso['status']) {
                                    'aberto' => 'bg-blue-100 text-blue-800',
                                    'em_analise' => 'bg-yellow-100 text-yellow-800',
                                    'deferido' => 'bg-green-100 text-green-800',
                                    'indeferido' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                $statusText = match($recurso['status']) {
                                    'aberto' => 'Aguardando resposta',
                                    'em_analise' => 'Em análise',
                                    'deferido' => 'Deferido',
                                    'indeferido' => 'Indeferido',
                                    default => ucfirst($recurso['status'])
                                };
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de detalhes do recurso -->
<div id="modal-recurso" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <!-- Cabeçalho do modal -->
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-xl font-bold text-gray-800" id="modal-titulo">Detalhes do Recurso</h2>
                    <button onclick="fecharModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Conteúdo do modal -->
                <div id="modal-conteudo">
                    <!-- Será preenchido via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filtros e busca
document.getElementById('filtro-status').addEventListener('change', filtrarRecursos);
document.getElementById('busca').addEventListener('input', filtrarRecursos);

function filtrarRecursos() {
    const status = document.getElementById('filtro-status').value;
    const busca = document.getElementById('busca').value.toLowerCase();
    const recursos = document.querySelectorAll('.recurso-item');
    
    recursos.forEach(recurso => {
        const statusRecurso = recurso.dataset.status;
        const pss = recurso.dataset.pss;
        const etapa = recurso.dataset.etapa;
        const protocolo = recurso.dataset.protocolo;
        
        const matchStatus = !status || statusRecurso === status;
        const matchBusca = !busca || pss.includes(busca) || etapa.includes(busca) || protocolo.includes(busca);
        
        if (matchStatus && matchBusca) {
            recurso.style.display = 'block';
        } else {
            recurso.style.display = 'none';
        }
    });
}

// Modal
function abrirModalRecurso(recurso) {
    const modal = document.getElementById('modal-recurso');
    const titulo = document.getElementById('modal-titulo');
    const conteudo = document.getElementById('modal-conteudo');
    
    titulo.textContent = `Recurso - ${recurso.pss_titulo || 'PSS não informado'}`;
    
    const statusClass = getStatusClass(recurso.status);
    const statusText = getStatusText(recurso.status);
    
    conteudo.innerHTML = `
        <div class="space-y-6">
            <!-- Informações básicas -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>Etapa:</strong> ${recurso.etapa_nome || 'Não informado'}</div>
                    <div><strong>Cargo:</strong> ${recurso.cargo_nome || 'Não informado'}</div>
                    <div><strong>Protocolo:</strong> ${recurso.protocolo || 'Não informado'}</div>
                    <div><strong>Status:</strong> <span class="px-2 py-1 rounded text-xs ${statusClass}">${statusText}</span></div>
                </div>
            </div>
            
            <!-- Motivo do indeferimento -->
            ${recurso.motivo_abertura ? `
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <h4 class="font-semibold text-red-800 mb-2">Motivo do indeferimento:</h4>
                    <p class="text-red-700">${recurso.motivo_abertura.replace(/\n/g, '<br>')}</p>
                </div>
            ` : ''}
            
            <!-- Resposta do candidato -->
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <h4 class="font-semibold text-blue-800 mb-2">Sua resposta:</h4>
                ${recurso.resposta_candidato ? `
                    <p class="text-blue-700 mb-4">${recurso.resposta_candidato.replace(/\n/g, '<br>')}</p>
                ` : `
                    <!-- Aviso importante -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-2"></i>
                            <div class="text-sm text-yellow-800">
                                <strong>Atenção:</strong> Após enviar sua resposta, não será possível editá-la. Certifique-se de revisar cuidadosamente antes de enviar.
                            </div>
                        </div>
                    </div>
                    
                    <form id="form-resposta" onsubmit="enviarResposta(event, ${recurso.id})">
                        <div class="mb-3">
                            <textarea 
                                id="textarea-resposta" 
                                name="resposta" 
                                rows="6" 
                                maxlength="2000"
                                class="w-full border border-gray-300 rounded px-3 py-2 resize-none" 
                                placeholder="Digite sua resposta ao recurso..." 
                                required
                                oninput="atualizarContador(this)"
                            ></textarea>
                            
                            <!-- Contador de caracteres -->
                            <div class="flex justify-between items-center mt-2 text-sm">
                                <div id="contador-caracteres" class="text-gray-600">
                                    <span id="caracteres-digitados">0</span> / 2.000 caracteres
                                </div>
                                <div id="aviso-limite" class="text-red-600 hidden">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Limite máximo atingido
                                </div>
                            </div>
                            
                            <!-- Barra de progresso -->
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                <div id="barra-progresso" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <!-- Mensagem de erro -->
                        <div id="mensagem-erro" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-red-600 mt-1 mr-2"></i>
                                <div class="text-sm text-red-800" id="texto-erro"></div>
                            </div>
                        </div>
                        
                        <div class="flex gap-2">
                            <button type="submit" id="btn-enviar" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                                Enviar Resposta
                            </button>
                            <button type="button" onclick="fecharModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">
                                Cancelar
                            </button>
                        </div>
                    </form>
                `}
            </div>
            
            <!-- Decisão da comissão -->
            ${recurso.motivo_decisao ? `
                <div class="bg-${recurso.status === 'deferido' ? 'green' : 'red'}-50 p-4 rounded-lg border border-${recurso.status === 'deferido' ? 'green' : 'red'}-200">
                    <h4 class="font-semibold text-${recurso.status === 'deferido' ? 'green' : 'red'}-800 mb-2">Decisão da comissão:</h4>
                    <p class="text-${recurso.status === 'deferido' ? 'green' : 'red'}-700">${recurso.motivo_decisao.replace(/\n/g, '<br>')}</p>
                    <p class="text-sm text-gray-500 mt-2">Decidido em: ${new Date(recurso.dt_decisao).toLocaleString('pt-BR')}</p>
                </div>
            ` : ''}
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function fecharModal() {
    document.getElementById('modal-recurso').classList.add('hidden');
}

function getStatusClass(status) {
    switch(status) {
        case 'aberto': return 'bg-blue-100 text-blue-800';
        case 'em_analise': return 'bg-yellow-100 text-yellow-800';
        case 'deferido': return 'bg-green-100 text-green-800';
        case 'indeferido': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'aberto': return 'Aguardando resposta';
        case 'em_analise': return 'Em análise';
        case 'deferido': return 'Deferido';
        case 'indeferido': return 'Indeferido';
        default: return status;
    }
}

// Função para atualizar contador de caracteres
function atualizarContador(textarea) {
    const caracteresDigitados = textarea.value.length;
    const limite = 2000;
    const porcentagem = (caracteresDigitados / limite) * 100;
    
    // Atualizar contador
    document.getElementById('caracteres-digitados').textContent = caracteresDigitados;
    
    // Atualizar barra de progresso
    const barraProgresso = document.getElementById('barra-progresso');
    barraProgresso.style.width = porcentagem + '%';
    
    // Mudar cor da barra conforme proximidade do limite
    if (porcentagem >= 90) {
        barraProgresso.className = 'bg-red-600 h-2 rounded-full transition-all duration-300';
        document.getElementById('contador-caracteres').className = 'text-red-600 font-semibold';
    } else if (porcentagem >= 75) {
        barraProgresso.className = 'bg-yellow-600 h-2 rounded-full transition-all duration-300';
        document.getElementById('contador-caracteres').className = 'text-yellow-600 font-semibold';
    } else {
        barraProgresso.className = 'bg-blue-600 h-2 rounded-full transition-all duration-300';
        document.getElementById('contador-caracteres').className = 'text-gray-600';
    }
    
    // Mostrar/ocultar aviso de limite
    const avisoLimite = document.getElementById('aviso-limite');
    if (caracteresDigitados >= limite) {
        avisoLimite.classList.remove('hidden');
    } else {
        avisoLimite.classList.add('hidden');
    }
    
    // Habilitar/desabilitar botão de envio
    const btnEnviar = document.getElementById('btn-enviar');
    if (caracteresDigitados < 10) {
        btnEnviar.disabled = true;
        btnEnviar.title = 'A resposta deve ter pelo menos 10 caracteres';
    } else {
        btnEnviar.disabled = false;
        btnEnviar.title = '';
    }
}

function mostrarErro(mensagem) {
    const mensagemErro = document.getElementById('mensagem-erro');
    const textoErro = document.getElementById('texto-erro');
    
    textoErro.textContent = mensagem;
    mensagemErro.classList.remove('hidden');
    
    // Ocultar após 5 segundos
    setTimeout(() => {
        mensagemErro.classList.add('hidden');
    }, 5000);
}

function enviarResposta(event, recursoId) {
    event.preventDefault();
    const form = event.target;
    const resposta = form.resposta.value.trim();
    
    // Validações
    if (resposta.length < 10) {
        mostrarErro('A resposta deve ter pelo menos 10 caracteres.');
        return;
    }
    
    if (resposta.length > 2000) {
        mostrarErro('A resposta não pode ter mais de 2.000 caracteres.');
        return;
    }
    
    // Desabilitar botão durante envio
    const btnEnviar = document.getElementById('btn-enviar');
    const textoOriginal = btnEnviar.textContent;
    btnEnviar.disabled = true;
    btnEnviar.textContent = 'Enviando...';
    
    // Fazer requisição
    fetch('/painel/recurso/responder', {
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
            // Mostrar mensagem de sucesso no modal
            const conteudo = document.getElementById('modal-conteudo');
            conteudo.innerHTML = `
                <div class="text-center py-8">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-4">
                        <i class="fas fa-check-circle text-green-600 text-4xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Resposta enviada com sucesso!</h3>
                        <p class="text-green-700">${data.message}</p>
                    </div>
                    <button onclick="location.reload()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Atualizar página
                    </button>
                </div>
            `;
        } else {
            mostrarErro(data.message || 'Erro ao enviar resposta');
            btnEnviar.disabled = false;
            btnEnviar.textContent = textoOriginal;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        mostrarErro('Erro de conexão. Tente novamente.');
        btnEnviar.disabled = false;
        btnEnviar.textContent = textoOriginal;
    });
}

// Fechar modal ao clicar fora
document.getElementById('modal-recurso').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModal();
    }
});
</script>

