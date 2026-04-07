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
                <li><a href="/pss/<?= $pss['id'] ?>/inscricao" class="hover:text-green-600">Escolher Zona</a></li>
                <li>/</li>
                <li class="text-gray-800 font-semibold">Zona <?= ucfirst($zona) ?></li>
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
                            Vagas da Zona <?= ucfirst($zona) ?>
                        </h1>
                        <p class="text-lg text-gray-600 mb-2">
                            <?= htmlspecialchars($pss['titulo']) ?>
                        </p>
                        <p class="text-sm text-gray-500">
                            Escolha a vaga desejada para prosseguir com a inscrição.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de cargos/vagas -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Cargos Disponíveis - Zona <?= ucfirst($zona) ?></h2>
            </div>
            
            <?php if (empty($cargos)): ?>
                <div class="p-6 text-center">
                    <p class="text-gray-600">Nenhum cargo disponível para esta zona.</p>
                </div>
            <?php else: ?>
                <?php foreach ($cargos as $cargo): ?>
                    <div class="p-6 border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-5">
                            <!-- Informações do cargo -->
                            <div class="flex flex-col gap-2 w-full">
                                <h3 class="font-bold text-lg text-gray-800">
                                    <?= htmlspecialchars($cargo['nome']) ?>
                                </h3>
                                
                                <div class="flex gap-4 text-sm text-gray-600">
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
                                
                                <?php if (!empty($cargo['requisitos_texto'])): ?>
                                    <p class="text-sm text-gray-700 mt-2">
                                        <strong>Requisitos:</strong> <?= htmlspecialchars($cargo['requisitos_texto']) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo['salario_base'])): ?>
                                    <p class="text-sm text-green-700 font-semibold">
                                        <strong>Salário:</strong> R$ <?= number_format($cargo['salario_base'], 2, ',', '.') ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if (!empty($cargo['secretaria'])): ?>
                                    <p class="text-sm text-gray-600">
                                        <strong>Secretaria:</strong> <?= htmlspecialchars($cargo['secretaria']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Ações -->
                            <div class="flex flex-col justify-center items-center gap-3">
                                <div class="flex flex-col justify-center items-center">
                                    <span class="text-2xl font-bold text-green-700"><?= $cargo['vagas_total'] ?></span>
                                    <p class="uppercase text-xs text-center">Vagas</p>
                                </div>
                                
                                <a href="/pss/<?= $pss['id'] ?>/cargo/<?= $cargo['id'] ?>/inscricao" 
                                   class="inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-4 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                                    Inscreva-se
                                </a>
                                
                                <a href="/pss/<?= $pss['id'] ?>/cargo/<?= $cargo['id'] ?>" 
                                   class="inline-block cursor-pointer align-middle rounded-[3px] border border-gray-400 bg-white px-3 py-1 text-xs font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                                    Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Botões de navegação -->
        <div class="flex justify-center gap-4 mt-8">
            <a href="/pss/<?= $pss['id'] ?>/inscricao" 
               class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-white px-6 py-3 text-sm font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                Voltar às Zonas
            </a>
        </div>
    </div>
</section>

