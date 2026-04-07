<?php $this->layout("template") ?>

<section class="w-full mx-auto mt-20 lg:mt-40 bg-white shadow-md rounded-lg p-3 lg:p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full flex-1 text-zinc-800">
        
        <!-- Breadcrumb -->
        <nav class="mb-4 lg:mb-6">
            <ol class="flex items-center space-x-1 lg:space-x-2 text-xs lg:text-sm text-gray-600 flex-wrap">
                <li><a href="/" class="hover:text-green-600">Início</a></li>
                <li>/</li>
                <li><a href="/pss/<?= $pss['id'] ?>" class="hover:text-green-600 truncate max-w-[100px] lg:max-w-none"><?= htmlspecialchars($pss['titulo']) ?></a></li>
                <li>/</li>
                <li><a href="/onboarding/<?= $pss['id'] ?>/zona" class="hover:text-green-600">Zona <?= ucfirst($zona) ?></a></li>
                <li>/</li>
                <li class="text-gray-800 font-semibold">Escolher Microrregião</li>
            </ol>
        </nav>

        <!-- Cabeçalho -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-4 lg:mb-6">
            <div class="p-3 lg:p-6 border-b border-gray-200">
                <div class="flex flex-col lg:flex-row items-center lg:items-start gap-3 lg:gap-6">
                    <!-- Logo/Brasão -->
                    <div class="h-16 w-20 lg:h-24 lg:w-40 flex items-center justify-center border border-gray-300 rounded shadow-sm flex-shrink-0">
                        <img src="/assets/brasao.png" alt="Brasão" class="max-h-12 lg:max-h-20">
                    </div>
                    
                    <!-- Informações principais -->
                    <div class="flex-1 text-center lg:text-left">
                        <h1 class="text-xl lg:text-3xl font-bold text-gray-800 mb-2 lg:mb-4">
                            Etapa 2: Escolha sua Microrregião
                        </h1>
                        <p class="text-sm lg:text-lg text-gray-600 mb-1 lg:mb-2">
                            <?= htmlspecialchars($pss['titulo']) ?> - Zona <?= ucfirst($zona) ?>
                        </p>
                        <p class="text-xs lg:text-sm text-gray-500">
                            Agora selecione a microrregião específica onde deseja atuar. Isso refinará ainda mais as vagas mostradas para você.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progresso do onboarding -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-4 lg:mb-6 p-3 lg:p-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-0">
                <div class="flex items-center">
                    <div class="w-6 h-6 lg:w-8 lg:h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-xs lg:text-sm font-bold">✓</div>
                    <span class="ml-2 text-xs lg:text-sm font-semibold text-green-600">Zona Escolhida</span>
                </div>
                <div class="flex-1 mx-4 h-1 bg-gray-200 rounded">
                    <div class="h-1 bg-green-600 rounded" style="width: 100%"></div>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <span class="ml-2 text-sm font-semibold text-green-600">Escolher Microrregião</span>
                </div>
            </div>
        </div>

        <!-- Lista de microrregiões -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Microrregiões Disponíveis na Zona <?= ucfirst($zona) ?></h2>
                <p class="text-sm text-gray-600 mt-1">Selecione a microrregião onde você gostaria de trabalhar</p>
            </div>
            
            <?php if (empty($microrregioes)): ?>
                <div class="p-6 text-center">
                    <p class="text-gray-600 mb-4">Nenhuma microrregião específica disponível para esta zona.</p>
                    <p class="text-sm text-gray-500 mb-6">Você será direcionado para ver todas as vagas da zona selecionada.</p>
                    <a href="/pss/<?= $pss['id'] ?>/vagas?zona=<?= $zona ?>" 
                       class="inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                        Ver Vagas da Zona
                    </a>
                </div>
            <?php else: ?>
                <!-- Opção para ver todas as microrregiões -->
                <div class="p-6 border-b border-gray-200 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                Todas as Microrregiões
                            </h3>
                            <p class="text-sm text-gray-600">
                                Ver todas as vagas disponíveis na Zona <?= ucfirst($zona) ?>, independente da microrregião.
                            </p>
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <a href="/pss/<?= $pss['id'] ?>/vagas?zona=<?= $zona ?>" 
                               class="inline-block cursor-pointer align-middle rounded-md border border-blue-600 bg-blue-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-blue-700 transition-colors">
                                Ver Todas
                            </a>
                        </div>
                    </div>
                </div>

                <?php foreach ($microrregioes as $microrregiao): ?>
                    <div class="p-3 lg:p-6 border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4 lg:justify-between">
                            <!-- Informações da microrregião -->
                            <div class="flex-1 text-center lg:text-left">
                                <h3 class="text-lg lg:text-xl font-bold text-gray-800 mb-2">
                                    Microrregião <?= ucfirst($microrregiao['microrregiao']) ?>
                                </h3>
                                
                                <div class="flex flex-wrap justify-center lg:justify-start gap-2 lg:gap-4 text-xs lg:text-sm text-gray-600 mb-2">
                                    <span class="bg-green-100 text-green-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                        <?= $microrregiao['total_cargos'] ?> Cargo(s) Disponível(is)
                                    </span>
                                    <span class="bg-blue-100 text-blue-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                        <?= $microrregiao['total_vagas'] ?> Vaga(s) Total
                                    </span>
                                </div>
                                
                                <p class="text-xs lg:text-sm text-gray-600">
                                    Vagas específicas para a microrregião <?= ucfirst($microrregiao['microrregiao']) ?> na Zona <?= ucfirst($zona) ?>.
                                </p>
                            </div>
                            
                            <!-- Botão de ação -->
                            <div class="flex flex-col items-center gap-2 flex-shrink-0 w-full lg:w-auto">
                                <span class="text-xl lg:text-2xl font-bold text-green-700"><?= $microrregiao['total_vagas'] ?></span>
                                <p class="text-xs text-gray-600 text-center">Vagas</p>
                                
                                <a href="/pss/<?= $pss['id'] ?>/vagas?zona=<?= $zona ?>&microrregiao=<?= $microrregiao['microrregiao'] ?>" 
                                   class="inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-4 lg:px-6 py-2 lg:py-3 text-xs lg:text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors w-full lg:w-auto text-center">
                                    Escolher Esta
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Botões de navegação -->
        <div class="flex justify-center gap-4 mt-8">
            <a href="/onboarding/<?= $pss['id'] ?>/zona" 
               class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-white px-6 py-3 text-sm font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                Voltar à Escolha de Zona
            </a>
        </div>
    </div>
</section>

