<?php $this->layout("template") ?>

<section class="w-full mx-auto mt-40 bg-white shadow-md rounded-lg p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full flex-1 text-zinc-800">
        
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="/" class="hover:text-green-600">Início</a></li>
                <li>/</li>
                <li><a href="/pss/<?= $pss["id"] ?>" class="hover:text-green-600"><?= htmlspecialchars($pss["titulo"]) ?></a></li>
                <li>/</li>
                <li class="text-gray-800 font-semibold">Vagas Selecionadas</li>
            </ol>
        </nav>

        <!-- Cabeçalho -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="p-3 lg:p-6 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-6">
                    <!-- Logo/Brasão -->
                    <div class="h-16 w-20 lg:h-24 lg:w-40 flex items-center justify-center border border-gray-300 rounded shadow-sm mx-auto lg:mx-0 flex-shrink-0">
                        <img src="/assets/brasao.png" alt="Brasão" class="max-h-14 lg:max-h-20">
                    </div>
                    
                    <!-- Informações principais -->
                    <div class="flex-1 text-center lg:text-left">
                        <h1 class="text-xl lg:text-3xl font-bold text-gray-800 mb-2 lg:mb-4">
                            Vagas Selecionadas para Você
                        </h1>
                        <p class="text-sm lg:text-lg text-gray-600 mb-2">
                            <?= htmlspecialchars($pss["titulo"]) ?>
                        </p>
                        <div class="flex flex-wrap justify-center lg:justify-start gap-2 text-xs lg:text-sm">
                            <span class="bg-blue-100 text-blue-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                Zona: <?= ucfirst($zona_filtro) ?>
                            </span>
                            <?php if ($microrregiao_filtro !== "todas"): ?>
                                <span class="bg-indigo-100 text-indigo-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                    Microrregião: <?= ucfirst($microrregiao_filtro) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Ações -->
                    <div class="flex flex-col items-center gap-4 flex-shrink-0 w-full lg:w-auto">
                        <div class="text-center">
                            <span class="text-2xl lg:text-4xl font-bold text-green-700"><?= count($cargos) ?></span>
                            <p class="text-xs lg:text-sm text-gray-600">Cargo(s) Encontrado(s)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo da seleção -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-green-800">Filtros Aplicados com Sucesso!</h3>
                    <p class="text-sm text-green-700">
                        Encontramos <?= count($cargos) ?> cargo(s) que correspondem às suas preferências.
                        <?php if ($microrregiao_filtro !== "todas"): ?>
                            Você pode alterar sua seleção a qualquer momento.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Lista de cargos/vagas -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Cargos Disponíveis</h2>
            </div>
            
            <?php if (empty($cargos)): ?>
                <div class="p-10 text-center">
                    <div class="mb-4">
                        <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Nenhum cargo encontrado</h3>
                    <p class="text-gray-600 mb-6">
                        Não encontramos cargos para os critérios selecionados. 
                        Que tal tentar uma seleção diferente?
                    </p>
                    <div class="flex justify-center gap-4">
                        <a href="/onboarding/<?= $pss["id"] ?>/zona" 
                           class="inline-block cursor-pointer align-middle rounded-md border border-blue-600 bg-blue-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-blue-700 transition-colors">
                            Alterar Seleção
                        </a>
                        <a href="/pss/<?= $pss["id"] ?>" 
                           class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-white px-6 py-3 text-sm font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                            Ver Todas as Vagas
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($cargos as $cargo): ?>
                    <div class="p-3 lg:p-6 border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-start gap-4 lg:gap-5">
                            <!-- Informações do cargo -->
                            <div class="flex flex-col gap-2 w-full text-center lg:text-left">
                                <h3 class="font-bold text-lg lg:text-xl text-gray-800">
                                    <?= htmlspecialchars($cargo["nome"]) ?>
                                </h3>
                                
                                <div class="flex flex-wrap justify-center lg:justify-start gap-2 lg:gap-4 text-xs lg:text-sm text-gray-600">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded whitespace-nowrap">
                                        Zona: <?= ucfirst($cargo["zona"]) ?>
                                    </span>
                                    <?php if (!empty($cargo["microrregiao"])): ?>
                                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded whitespace-nowrap">
                                            Microrregião: <?= ucfirst($cargo["microrregiao"]) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded whitespace-nowrap">
                                        <?= $cargo["vagas_total"] ?> Vagas
                                    </span>
                                    <?php if (isset($cargo["vagas_reserva"]) && $cargo["vagas_reserva"] > 0): ?>
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded whitespace-nowrap">
                                            <?= $cargo["vagas_reserva"] ?> CR
                                        </span>
                                    <?php endif; ?>
                                    <?php if (isset($cargo["vagas_pcd"]) && $cargo["vagas_pcd"] > 0): ?>
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded whitespace-nowrap">
                                            <?= $cargo["vagas_pcd"] ?> PCD
                                        </span>
                                    <?php endif; ?>
                                    <?php if (isset($cargo["vagas_ppp"]) && $cargo["vagas_ppp"] > 0): ?>
                                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded whitespace-nowrap">
                                            <?= $cargo["vagas_ppp"] ?> PPP
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if (!empty($cargo["requisitos_texto"])): ?>
                                    <p class="text-xs lg:text-sm text-gray-700 mt-2">
                                        <strong>Requisitos:</strong> <?= htmlspecialchars($cargo["requisitos_texto"]) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo["salario_base"])): ?>
                                    <p class="text-xs lg:text-sm text-green-700 font-semibold">
                                        <strong>Salário:</strong> R$ <?= number_format($cargo["salario_base"], 2, ",", ".") ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo["secretaria"])): ?>
                                    <p class="text-xs lg:text-sm text-gray-600">
                                        <strong>Secretaria:</strong> <?= htmlspecialchars($cargo["secretaria"]) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Ações -->
                            <div class="flex flex-col justify-center items-center gap-3 flex-shrink-0 w-full lg:w-auto">
                                <div class="flex flex-col justify-center items-center">
                                    <span class="text-xl lg:text-2xl font-bold text-green-700"><?= $cargo["vagas_total"] ?></span>
                                    <p class="uppercase text-xs text-center">Vagas</p>
                                </div>
                                
                                <?php if (isset($_SESSION["usuario"])): ?>
                                    <a href="/inscricao/<?= $pss["id"] ?>/<?= $cargo["id"] ?>" 
                                       class="inline-block cursor-pointer align-middle rounded-[3px] border border-green-600 bg-green-600 px-4 py-2 text-xs font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors w-full lg:w-auto text-center">
                                        Inscreva-se
                                    </a>
                                <?php else: ?>
                                    <a href="/login" 
                                       class="inline-block cursor-pointer align-middle rounded-[3px] border border-blue-600 bg-blue-600 px-4 py-2 text-xs font-bold uppercase text-white no-underline shadow hover:bg-blue-700 transition-colors w-full lg:w-auto text-center">
                                        Fazer Login
                                    </a>
                                <?php endif; ?>
                                
                                <a href="/pss/<?= $pss["id"] ?>/cargo/<?= $cargo["id"] ?>" 
                                   class="inline-block cursor-pointer align-middle rounded-[3px] border border-gray-400 bg-white px-4 py-2 text-xs font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors w-full lg:w-auto text-center">
                                    Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Botões de ação -->
        <div class="flex justify-center gap-4 mt-8">
            <a href="/onboarding/<?= $pss["id"] ?>/zona" 
               class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-white px-6 py-3 text-sm font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                Alterar Seleção
            </a>
            
            <a href="/pss/<?= $pss["id"] ?>" 
               class="inline-block cursor-pointer align-middle rounded-md border border-blue-600 bg-blue-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-blue-700 transition-colors">
                Ver Todas as Vagas
            </a>
        </div>
    </div>
</section>

