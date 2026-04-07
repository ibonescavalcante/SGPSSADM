<?php $this->layout("template") ?>

<section class="w-full mx-auto mt-40 bg-white shadow-md rounded-lg p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full flex-1 text-zinc-800">
        
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="/" class="hover:text-green-600">Início</a></li>
                <li>/</li>
                <li><a href="/pss/<?= $pss['id'] ?>" class="hover:text-green-600"><?= htmlspecialchars($pss['titulo']) ?></a></li>
                <li>/</li>
                <li class="text-gray-800 font-semibold">Escolher Zona</li>
            </ol>
        </nav>

        <!-- Cabeçalho -->
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
                            Escolha a Zona de Atuação
                        </h1>
                        <p class="text-lg text-gray-600 mb-2">
                            <?= htmlspecialchars($pss['titulo']) ?>
                        </p>
                        <p class="text-sm text-gray-500">
                            Selecione a zona onde deseja atuar para ver as vagas disponíveis.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de zonas -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Zonas Disponíveis</h2>
            </div>
            
            <?php if (empty($zonas)): ?>
                <div class="p-6 text-center">
                    <p class="text-gray-600">Nenhuma zona disponível para este processo.</p>
                </div>
            <?php else: ?>
                <?php foreach ($zonas as $zona): ?>
                    <div class="p-6 border-b border-gray-200 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <!-- Informações da zona -->
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">
                                    Zona <?= ucfirst($zona['zona']) ?>
                                </h3>
                                
                                <div class="flex gap-4 text-sm text-gray-600">
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                        <?= $zona['total_cargos'] ?> Cargo(s) Disponível(is)
                                    </span>
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                                        <?= $zona['total_vagas'] ?> Vaga(s) Total
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Botão de ação -->
                            <div class="flex flex-col items-center gap-2">
                                <span class="text-2xl font-bold text-green-700"><?= $zona['total_vagas'] ?></span>
                                <p class="text-xs text-gray-600 text-center">Vagas</p>
                                
                                <a href="/pss/<?= $pss['id'] ?>/zona/<?= $zona['zona'] ?>" 
                                   class="inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                                    Escolher Esta Zona
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Botões de navegação -->
        <div class="flex justify-center gap-4 mt-8">
            <a href="/pss/<?= $pss['id'] ?>" 
               class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-white px-6 py-3 text-sm font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                Voltar aos Detalhes
            </a>
        </div>
    </div>
</section>

