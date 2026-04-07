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
                <li class="text-gray-800 font-semibold truncate max-w-[150px] lg:max-w-none"><?= htmlspecialchars($cargo['nome']) ?></li>
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
                        <p class="text-xs lg:text-sm text-gray-600 mb-1 lg:mb-2">
                            <?= htmlspecialchars($pss['titulo']) ?>
                        </p>
                        <h1 class="text-xl lg:text-3xl font-bold text-gray-800 mb-2 lg:mb-4">
                            <?= htmlspecialchars($cargo['nome']) ?>
                        </h1>
                        
                        <div class="flex flex-wrap justify-center lg:justify-start gap-2 lg:gap-4 text-xs lg:text-sm">
                            <span class="bg-blue-100 text-blue-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                Zona: <?= ucfirst($cargo['zona']) ?>
                            </span>
                            <span class="bg-green-100 text-green-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                <?= $cargo["vagas_total"] ?> Vagas
                            </span>
                            <?php if (isset($cargo["vagas_reserva"]) && $cargo["vagas_reserva"] > 0): ?>
                                <span class="bg-yellow-100 text-yellow-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                    <?= $cargo["vagas_reserva"] ?> CR
                                </span>
                            <?php endif; ?>
                            <?php if (isset($cargo["vagas_pcd"]) && $cargo["vagas_pcd"] > 0): ?>
                                <span class="bg-purple-100 text-purple-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                    <?= $cargo["vagas_pcd"] ?> PCD
                                </span>
                            <?php endif; ?>
                            <?php if (isset($cargo["vagas_ppp"]) && $cargo["vagas_ppp"] > 0): ?>
                                <span class="bg-orange-100 text-orange-800 px-2 lg:px-3 py-1 rounded-full whitespace-nowrap">
                                    <?= $cargo["vagas_ppp"] ?> PPP
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Ações -->
                    <div class="flex flex-col items-center gap-4">
                        <div class="text-center">
                            <span class="text-4xl font-bold text-green-700"><?= $cargo['vagas_total'] ?></span>
                            <p class="text-sm text-gray-600">Vagas Totais</p>
                        </div>
                        
                        <?php
                        // ✅ CORREÇÃO: Verificar período de inscrição com validação de campos
                        $periodo_ativo = false;
                        $agora = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
                        
                        if (!empty($pss['inscricao_ini']) && !empty($pss['inscricao_fim'])) {
                            try {
                                $inicio = new DateTime($pss['inscricao_ini']);
                                $inicio->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                                
                                $fim = new DateTime($pss['inscricao_fim']);
                                $fim->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                                
                                $periodo_ativo = ($agora >= $inicio && $agora <= $fim);
                            } catch (Exception $e) {
                                error_log("Erro ao processar datas: " . $e->getMessage());
                                $periodo_ativo = false;
                            }
                        }
                        ?>
                        
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <?php if ($periodo_ativo): ?>
                                <a href="/onboarding/<?= $pss['id'] ?>/zona" 
                                   class="inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                                    Inscreva-se
                                </a>
                            <?php else: ?>
                                <button class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-gray-400 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow" disabled>
                                    <?php if (!empty($pss['inscricao_ini']) && $agora < $inicio): ?>
                                        Inscrições em Breve
                                    <?php else: ?>
                                        Inscrições Encerradas
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/login" 
                               class="inline-block cursor-pointer align-middle rounded-md border border-blue-600 bg-blue-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-blue-700 transition-colors">
                                Fazer Login para se Inscrever
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalhes da vaga -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Informações da vaga -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Detalhes do Cargo</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cargo:</label>
                        <p class="text-gray-800"><?= htmlspecialchars($cargo['nome']) ?></p>
                    </div>
                    
                    <?php if (!empty($cargo['secretaria'])): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Secretaria:</label>
                        <p class="text-gray-800"><?= htmlspecialchars($cargo['secretaria']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Zona de Atuação:</label>
                        <p class="text-gray-800"><?= ucfirst($cargo['zona']) ?></p>
                    </div>
                    
                    <?php if (!empty($cargo['microrregiao'])): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Microrregião:</label>
                        <p class="text-gray-800"><?= ucfirst($cargo['microrregiao']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($cargo['nivel'])): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nível:</label>
                        <p class="text-gray-800"><?= htmlspecialchars($cargo['nivel']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($cargo['carga_horaria'])): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Carga Horária:</label>
                        <p class="text-gray-800"><?= htmlspecialchars($cargo['carga_horaria']) ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Total de Vagas:</label>
                            <p class="text-gray-800 font-bold text-lg"><?= $cargo['vagas_total'] ?></p>
                        </div>
                        
                        <?php if ($cargo['vagas_pcd'] > 0): ?>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Vagas PCD:</label>
                            <p class="text-gray-800 font-bold text-lg"><?= $cargo['vagas_pcd'] ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($cargo['vagas_ppp'] > 0): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Vagas PPP:</label>
                        <p class="text-gray-800 font-bold text-lg"><?= $cargo['vagas_ppp'] ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($cargo["vagas_reserva"]) && $cargo["vagas_reserva"] > 0): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Vagas Cadastro Reserva:</label>
                        <p class="text-gray-800 font-bold text-lg"><?= $cargo["vagas_reserva"] ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($cargo['cadastro_reserva']): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cadastro de Reserva:</label>
                        <p class="text-green-600 font-semibold">Sim</p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($cargo['salario_base'])): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Salário:</label>
                        <p class="text-green-700 font-bold text-lg">R$ <?= number_format($cargo['salario_base'], 2, ',', '.') ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Requisitos -->
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Requisitos</h2>
                
                <?php if (!empty($cargo['requisitos_texto'])): ?>
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($cargo['requisitos_texto'])) ?></p>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Requisitos não informados.</p>
                <?php endif; ?>
                
                <?php if (!empty($cargo['descricao'])): ?>
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Descrição das Atividades</h3>
                        <div class="prose prose-sm max-w-none">
                            <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($cargo['descricao'])) ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informações do Processo -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Informações do Processo</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php if (!empty($pss['descricao'])): ?>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Descrição:</label>
                    <p class="text-gray-800"><?= nl2br(htmlspecialchars($pss['descricao'])) ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($pss['inscricao_ini'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Início das Inscrições:</label>
                    <?php
                    // ✅ CORREÇÃO: Usar campo correto e validar antes de formatar
                    try {
                        $data_inicio = new DateTime($pss['inscricao_ini']);
                        $data_inicio->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                        echo '<p class="text-gray-800">' . $data_inicio->format('d/m/Y H:i') . '</p>';
                    } catch (Exception $e) {
                        echo '<p class="text-gray-500 italic">Data não disponível</p>';
                    }
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($pss['inscricao_fim'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Fim das Inscrições:</label>
                    <?php
                    // ✅ CORREÇÃO: Usar campo correto e validar antes de formatar
                    try {
                        $data_fim = new DateTime($pss['inscricao_fim']);
                        $data_fim->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                        echo '<p class="text-gray-800">' . $data_fim->format('d/m/Y H:i') . '</p>';
                    } catch (Exception $e) {
                        echo '<p class="text-gray-500 italic">Data não disponível</p>';
                    }
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($pss['publicado_em'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Publicado em:</label>
                    <?php
                    try {
                        $data_publicacao = new DateTime($pss['publicado_em']);
                        $data_publicacao->setTimezone(new DateTimeZone('America/Sao_Paulo'));
                        echo '<p class="text-gray-800">' . $data_publicacao->format('d/m/Y') . '</p>';
                    } catch (Exception $e) {
                        echo '<p class="text-gray-500 italic">Data não disponível</p>';
                    }
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($pss['versao'])): ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Versão:</label>
                    <p class="text-gray-800"><?= htmlspecialchars($pss['versao']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botões de ação -->
        <div class="flex justify-center gap-4 mt-8">
            <a href="/pss/<?= $pss['id'] ?>" 
               class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-white px-6 py-3 text-sm font-bold uppercase text-gray-600 no-underline shadow hover:bg-gray-100 transition-colors">
                Voltar ao PSS
            </a>
            
            <?php if (isset($_SESSION['usuario'])): ?>
                <?php if ($periodo_ativo): ?>
                    <a href="/pss/<?= $pss['id'] ?>/cargo/<?= $cargo['id'] ?>/inscricao" 
                       class="inline-block cursor-pointer align-middle rounded-md border border-green-600 bg-green-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-green-700 transition-colors">
                        Inscrever-se Neste Cargo
                    </a>
                <?php else: ?>
                    <button class="inline-block cursor-pointer align-middle rounded-md border border-gray-400 bg-gray-400 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow" disabled>
                        <?php if (!empty($pss['inscricao_ini']) && isset($inicio) && $agora < $inicio): ?>
                            Inscrições em Breve
                        <?php else: ?>
                            Inscrições Encerradas
                        <?php endif; ?>
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <a href="/login" 
                   class="inline-block cursor-pointer align-middle rounded-md border border-blue-600 bg-blue-600 px-6 py-3 text-sm font-bold uppercase text-white no-underline shadow hover:bg-blue-700 transition-colors">
                    Fazer Login para se Inscrever
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
