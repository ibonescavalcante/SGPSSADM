<?php $this->layout("template") ?>

<section class="w-full mx-auto mt-40 bg-white shadow-md rounded-lg p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full flex-1 flex items-start gap-4 text-zinc-800">
        
        <!-- Sidebar com filtros -->
        <aside class="pt-16 flex-shrink-0">
            <h3 class="font-bold text-xl mb-4">FILTRAR POR ZONA:</h3>
            <ul class="flex flex-col gap-1">
                <li>
                    <a href="?zona=todas" 
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800 <?= (!isset($_GET['zona']) || $_GET['zona'] == 'todas') ? 'bg-green-800 text-white' : '' ?>">
                        Todas as Zonas
                    </a>
                </li>
                <li>
                    <a href="?zona=urbana" 
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800 <?= (isset($_GET['zona']) && $_GET['zona'] == 'urbana') ? 'bg-green-800 text-white' : '' ?>">
                        Zona Urbana
                    </a>
                </li>
                <li>
                    <a href="?zona=rural" 
                        class="block p-2 rounded-md whitespace-nowrap font-semibold transition-colors duration-300 text-gray-700 hover:text-gray-50 hover:bg-green-800 <?= (isset($_GET['zona']) && $_GET['zona'] == 'rural') ? 'bg-green-800 text-white' : '' ?>">
                        Zona Rural
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Conteúdo principal -->
        <div class="bg-white min-h-[46rem] w-full mt-8 shadow-md rounded overflow-hidden">
            <h2 class="text-3xl uppercase py-8 px-5 border-b border-b-gray-300 border-t-[6px] border-t-green-900 shadow-sm">
                Vagas Disponíveis
                <?php if (isset($_GET['zona']) && $_GET['zona'] != 'todas'): ?>
                    - <span class="font-bold">Zona <?= ucfirst($_GET['zona']) ?></span>
                <?php endif; ?>
            </h2>

            <?php if (empty($cargos)): ?>
                <div class="p-10 text-center">
                    <p class="text-gray-600">Nenhuma vaga disponível no momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($cargos as $cargo): ?>
                    <div class="block p-6 border-b border-b-gray-300 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-5">
                            <!-- Logo/Brasão -->
                            <div class="h-24 w-40 flex items-center justify-center border border-gray-300 rounded shadow-sm">
                                <img src="/assets/brasao.png" alt="Brasão" class="max-h-20">
                            </div>
                            
                            <!-- Informações da vaga -->
                            <div class="flex flex-col gap-2 w-full">
                                <p class="text-sm text-gray-600">
                                    <?= htmlspecialchars($cargo['pss_titulo']) ?>
                                </p>
                                <h3 class="font-bold text-xl text-gray-800">
                                    <?= htmlspecialchars($cargo['nome']) ?>
                                </h3>
                                <div class="flex gap-4 text-sm text-gray-600">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        Zona: <?= ucfirst($cargo['zona']) ?>
                                    </span>
                                    <span>Vagas: <?= $cargo['vagas_total'] ?></span>
                                    <?php if ($cargo['vagas_pcd'] > 0): ?>
                                        <span>PCD: <?= $cargo['vagas_pcd'] ?></span>
                                    <?php endif; ?>
                                    <?php if ($cargo['vagas_ppp'] > 0): ?>
                                        <span>PPP: <?= $cargo['vagas_ppp'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($cargo['requisitos_texto'])): ?>
                                    <p class="text-sm text-gray-700 mt-2">
                                        <strong>Requisitos:</strong> <?= htmlspecialchars($cargo['requisitos_texto']) ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($cargo['salario_base'])): ?>
                                    <p class="text-sm text-green-700 font-semibold">
                                        Salário: R$ <?= number_format($cargo['salario_base'], 2, ',', '.') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Ações -->
                            <div class="flex flex-col justify-center items-center gap-4">
                                <div class="flex flex-col justify-center items-center">
                                    <span class="text-3xl font-bold text-green-700"><?= $cargo['vagas_total'] ?></span>
                                    <p class="uppercase text-xs w-36 text-center">Vagas Disponíveis</p>
                                </div>
                                
                                <?php if (isset($_SESSION['usuario'])): ?>
                                    <a href="/inscricao/<?= $cargo['pss_id'] ?>/<?= $cargo['id'] ?>" 
                                       class="inline-block cursor-pointer align-middle rounded-[3px] border border-green-600 bg-green-600 px-4 py-2 text-xs font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                                        Inscreva-se
                                    </a>
                                <?php else: ?>
                                    <a href="/login" 
                                       class="inline-block cursor-pointer align-middle rounded-[3px] border border-blue-600 bg-blue-600 px-4 py-2 text-xs font-bold uppercase text-white no-underline shadow hover:bg-blue-700 transition-colors">
                                        Fazer Login
                                    </a>
                                <?php endif; ?>
                                
                                <a href="/pss/<?= $cargo["pss_id"] ?>/cargo/<?= $cargo["id"] ?>" 
                                   class="inline-block cursor-pointer align-middle rounded-[3px] border border-gray-400 bg-white px-3 py-1 text-xs font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                                    Mais Informações
                                </a>
                            </div>
                        </div>
                        
                        <!-- Detalhes expandíveis -->
                        <div id="detalhes-<?= $cargo['id'] ?>" class="hidden mt-4 p-4 bg-gray-50 rounded border">
                            <h4 class="font-bold mb-2">Detalhes da Vaga</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <strong>Zona:</strong> <?= ucfirst($cargo['zona']) ?>
                                </div>
                                <div>
                                    <strong>Total de Vagas:</strong> <?= $cargo['vagas_total'] ?>
                                </div>
                                <?php if ($cargo['vagas_pcd'] > 0): ?>
                                <div>
                                    <strong>Vagas PCD:</strong> <?= $cargo['vagas_pcd'] ?>
                                </div>
                                <?php endif; ?>
                                <?php if ($cargo['vagas_ppp'] > 0): ?>
                                <div>
                                    <strong>Vagas PPP:</strong> <?= $cargo['vagas_ppp'] ?>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <strong>Cadastro Reserva:</strong> <?= $cargo['cadastro_reserva'] ? 'Sim' : 'Não' ?>
                                </div>
                                <?php if (!empty($cargo['secretaria'])): ?>
                                <div>
                                    <strong>Secretaria:</strong> <?= htmlspecialchars($cargo['secretaria']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>



