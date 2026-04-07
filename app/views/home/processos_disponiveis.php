<?php $this->layout("template") ?>

<section class="w-full mx-auto mt-20 md:mt-40 bg-white shadow-md rounded-lg p-3 md:p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full flex-1 flex flex-col lg:flex-row items-start gap-4 text-zinc-800">

        <!-- Filtros Mobile - Dropdown -->
        <div class="w-full lg:hidden mb-4">
            <label for="status-mobile" class="block font-bold text-lg mb-2">FILTRAR POR STATUS:</label>
            <select id="status-mobile" onchange="window.location.href='?status='+this.value"
                class="w-full p-3 border border-gray-300 rounded-md bg-white font-semibold">
                <option value="todos" <?= (!isset($_GET['status']) || $_GET['status'] == 'todos') ? 'selected' : '' ?>>
                    Todos os Status
                </option>
                <option value="em_andamento"
                    <?= (isset($_GET['status']) && $_GET['status'] == 'em_andamento') ? 'selected' : '' ?>>
                    Em Andamento
                </option>
                <option value="rascunho"
                    <?= (isset($_GET['status']) && $_GET['status'] == 'rascunho') ? 'selected' : '' ?>>
                    Rascunho
                </option>
                <option value="finalizada"
                    <?= (isset($_GET['status']) && $_GET['status'] == 'finalizada') ? 'selected' : '' ?>>
                    Finalizado
                </option>
                <option value="cancelado"
                    <?= (isset($_GET['status']) && $_GET['status'] == 'cancelado') ? 'selected' : '' ?>>
                    Cancelado
                </option>
            </select>
        </div>

        <!-- Sidebar com filtros - Desktop -->
        <aside class="hidden lg:block pt-16 flex-shrink-0 min-w-[200px]">
            <h3 class="font-bold text-xl mb-4">FILTRAR POR STATUS:</h3>
            <ul class="flex flex-col gap-1">
                <li>
                    <a href="?status=todos"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800 <?= (!isset($_GET['status']) || $_GET['status'] == 'todos') ? 'bg-green-800 text-white' : '' ?>">
                        Todos os Status
                    </a>
                </li>
                <li>
                    <a href="?status=em_andamento"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800 <?= (isset($_GET['status']) && $_GET['status'] == 'em_andamento') ? 'bg-green-800 text-white' : '' ?>">
                        Em Andamento
                    </a>
                </li>
                <li>
                    <a href="?status=rascunho"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-yellow-600 <?= (isset($_GET['status']) && $_GET['status'] == 'rascunho') ? 'bg-yellow-600 text-white' : '' ?>">
                        Rascunho
                    </a>
                </li>
                <li>
                    <a href="?status=finalizada"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-blue-600 <?= (isset($_GET['status']) && $_GET['status'] == 'finalizada') ? 'bg-blue-600 text-white' : '' ?>">
                        Finalizado
                    </a>
                </li>
                <li>
                    <a href="?status=cancelado"
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-red-600 <?= (isset($_GET['status']) && $_GET['status'] == 'cancelado') ? 'bg-red-600 text-white' : '' ?>">
                        Cancelado
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Conteúdo principal -->
        <div class="bg-white min-h-[46rem] w-full lg:mt-8 shadow-md rounded overflow-hidden">
            <h2
                class="text-xl md:text-2xl lg:text-3xl uppercase py-4 md:py-6 lg:py-8 px-3 md:px-5 border-b border-b-gray-300 border-t-[6px] border-t-green-900 shadow-sm">
                Processos Seletivos Disponíveis
                <?php if (isset($_GET['status']) && $_GET['status'] != 'todos'): ?>
                - <span class="font-bold"><?= ucfirst(str_replace('_', ' ', $_GET['status'])) ?></span>
                <?php endif; ?>
            </h2>

            <?php if (empty($pss_ativos)): ?>
            <div class="p-10 text-center">
                <p class="text-gray-600">Nenhum processo seletivo disponível no momento.</p>
            </div>
            <?php else: ?>
            <?php foreach ($pss_ativos as $pss): ?>
            <div class="block p-3 md:p-6 border-b border-b-gray-300 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row items-start gap-3 md:gap-5">
                    <!-- Logo/Brasão -->
                    <div
                        class="h-16 w-20 md:h-24 md:w-40 flex items-center justify-center border border-gray-300 rounded shadow-sm flex-shrink-0 mx-auto md:mx-0">
                        <img src="/assets/brasao.png" alt="Brasão" class="max-h-12 md:max-h-20">
                    </div>

                    <!-- Informações do processo -->
                    <div class="flex flex-col gap-2 w-full">
                        <h3 class="font-bold text-lg md:text-xl text-gray-800 text-center md:text-left">
                            <?= htmlspecialchars($pss['titulo']) ?>
                        </h3>

                        <?php if (!empty($pss['secretaria'])): ?>
                        <p class="text-sm text-gray-600">
                            <strong>Secretaria:</strong> <?= htmlspecialchars($pss['secretaria']) ?>
                        </p>
                        <?php endif; ?>

                        <div
                            class="flex flex-wrap gap-2 text-xs md:text-sm text-gray-600 justify-center md:justify-start">
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded whitespace-nowrap">
                                <?= isset($pss['total_vagas']) ? $pss['total_vagas'] : '0' ?> Cargo(s)
                            </span>
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded whitespace-nowrap">
                                <?= isset($pss['total_posicoes']) ? $pss['total_posicoes'] : '0' ?> Vagas Totais
                            </span>
                            <?php if (isset($pss['total_pcd']) && $pss['total_pcd'] > 0): ?>
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded whitespace-nowrap">
                                <?= $pss['total_pcd'] ?> PCD
                            </span>
                            <?php endif; ?>
                            <?php if (isset($pss['total_ppp']) && $pss['total_ppp'] > 0): ?>
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded whitespace-nowrap">
                                <?= $pss['total_ppp'] ?> PPP
                            </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($pss['inscricao_ini']) && !empty($pss['inscricao_fim'])): ?>
                        <div class="text-sm text-gray-700 mt-2">
                            <strong>Período de Inscrições:</strong>
                            <?= date('d/m/Y', strtotime($pss['inscricao_ini'])) ?> a
                            <?= date('d/m/Y', strtotime($pss['inscricao_fim'])) ?>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($pss['ano_exercicio'])): ?>
                        <p class="text-sm text-gray-600">
                            <strong>Ano de Exercício:</strong> <?= $pss['ano_exercicio'] ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <!-- Ações -->
                    <div
                        class="flex flex-col justify-center items-center gap-3 mt-4 md:mt-0 w-full md:w-auto md:min-w-[180px] md:max-w-[200px] flex-shrink-0">
                        <div class="flex flex-col justify-center items-center">
                            <span
                                class="text-2xl md:text-3xl font-bold text-green-700"><?= $pss['total_posicoes'] ?></span>
                            <p class="uppercase text-xs text-center text-gray-600">Vagas Disponíveis</p>
                        </div>

                        <div class="flex flex-col gap-2 w-full max-w-[200px]">
                            <?php
                                    // Verificar se o período de inscrição está encerrado
                                    $agora = new DateTime();
                                    $fim_inscricao = new DateTime($pss['inscricao_fim'] ?? '1970-01-01');
                                    $prazo_encerrado = $agora > $fim_inscricao;
                                    ?>

                            <?php if ($prazo_encerrado): ?>
                            <button
                                onclick="mostrarModalEncerrado('<?= date('d/m/Y H:i', strtotime($pss['inscricao_fim'])) ?>')"
                                class="inline-block cursor-pointer align-middle rounded-[3px] border border-gray-400 bg-gray-400 px-3 py-2 text-xs font-bold uppercase text-white no-underline shadow opacity-60 text-center whitespace-nowrap hover:opacity-80 transition-opacity">
                                ENCERRADO
                            </button>
                            <div class="text-xs text-red-600 text-center mt-1">
                                <i class="fas fa-exclamation-triangle"></i>
                                Período de inscrições encerrado
                            </div>
                            <?php else: ?>
                            <a href="/onboarding/<?= $pss['id'] ?>/zona"
                                class="inline-block cursor-pointer align-middle rounded-[3px] border border-green-600 bg-green-600 px-3 py-2 text-xs font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors text-center whitespace-nowrap">
                                Inscreva-se
                            </a>
                            <?php endif; ?>

                            <a href="/pss/<?= $pss['id'] ?>"
                                class="inline-block cursor-pointer align-middle rounded-[3px] border border-gray-400 bg-white px-3 py-1 text-xs font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors text-center whitespace-nowrap">
                                Mais Informações
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modal de Período Encerrado -->
<div id="modalPeriodoEncerrado"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6 text-center">
            <!-- Ícone de Alerta -->
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>

            <!-- Título -->
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                Período de Inscrições Encerrado
            </h3>

            <!-- Mensagem -->
            <p class="text-gray-600 mb-2">
                O período de inscrições foi encerrado.
            </p>

            <!-- Data de encerramento -->
            <p class="text-sm text-gray-500 mb-6">
                Período encerrado em: <span id="dataEncerramento" class="font-medium"></span>
            </p>

            <!-- Botão Entendi -->
            <button onclick="fecharModalEncerrado()"
                class="w-full bg-green-600 text-white py-3 px-4 rounded-md font-semibold hover:bg-green-700 transition-colors">
                Entendi
            </button>
        </div>
    </div>
</div>

<script>
function mostrarModalEncerrado(dataEncerramento) {
    document.getElementById('dataEncerramento').textContent = dataEncerramento;
    document.getElementById('modalPeriodoEncerrado').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Previne scroll da página
}

function fecharModalEncerrado() {
    document.getElementById('modalPeriodoEncerrado').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restaura scroll da página
}

// Fechar modal ao clicar fora dele
document.getElementById('modalPeriodoEncerrado').addEventListener('click', function(e) {
    if (e.target === this) {
        fecharModalEncerrado();
    }
});

// Fechar modal com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        fecharModalEncerrado();
    }
});
</script>