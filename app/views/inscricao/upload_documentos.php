<?php $this->layout("template") ?>
<section class="w-full mx-auto mt-20 lg:mt-40 bg-white shadow-md rounded-lg p-3 lg:p-6 border border-gray-200">
    <div class="m-auto max-w-[60rem] w-full">
        
        <!-- Breadcrumb -->
        <nav class="flex mb-4 lg:mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-xs lg:text-sm flex-wrap">
                <li class="inline-flex items-center">
                    <a href="/" class="text-gray-700 hover:text-green-600">Início</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-1 lg:mx-2 text-gray-400">/</span>
                        <a href="/pss/<?= $pss['id'] ?>" class="text-gray-700 hover:text-green-600 truncate max-w-[150px] lg:max-w-none">
                            <?= htmlspecialchars($pss['titulo']) ?>
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-1 lg:mx-2 text-gray-400">/</span>
                        <span class="text-gray-500">Upload de Documentos</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Cabeçalho -->
        <div class="bg-white shadow-md rounded overflow-hidden mb-4 lg:mb-6">
            <div class="bg-green-800 text-white p-3 lg:p-6">
                <h1 class="text-lg lg:text-2xl font-bold">Upload de Documentos</h1>
                <p class="mt-1 lg:mt-2 text-sm lg:text-base"><?= htmlspecialchars($pss['titulo']) ?></p>
                <div class="text-green-100 text-xs lg:text-sm mt-1 lg:mt-2">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-1 lg:gap-0">
                        <span><strong>Cargo:</strong> <?= htmlspecialchars($cargo['nome']) ?></span>
                        <span class="hidden lg:inline mx-2">-</span>
                        <span><strong>Zona:</strong> <?= ucfirst($cargo['zona']) ?></span>
                        <?php if (!empty($cargo['microrregiao'])): ?>
                            <span class="hidden lg:inline mx-2">-</span>
                            <span><strong>Microrregião:</strong> <?= ucfirst($cargo['microrregiao']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dados do Candidato -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 lg:p-6 mb-4 lg:mb-6">
            <h3 class="text-base lg:text-lg font-medium text-blue-900 mb-3 lg:mb-4">Dados do Candidato</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 lg:gap-4 text-xs lg:text-sm">
                <div>
                    <strong>Nome:</strong> <?= htmlspecialchars($candidato['nome']) ?>
                </div>
                <div>
                    <strong>CPF:</strong> <?= htmlspecialchars($candidato['cpf']) ?>
                </div>
                <div>
                    <strong>Email:</strong> <?= htmlspecialchars($candidato['email']) ?>
                </div>
                <div>
                    <strong>Telefone:</strong> <?= htmlspecialchars($candidato['celular'] ?? 'Não informado') ?>
                </div>
                <?php if (!empty($candidato['data_nascimento'])): ?>
                <div>
                    <strong>Data de Nascimento:</strong> <?= date('d/m/Y', strtotime($candidato['data_nascimento'])) ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($candidato['documento_tipo']) && !empty($candidato['documento_numero'])): ?>
                <div>
                    <strong>Documento de Identidade:</strong> <?= htmlspecialchars($candidato['documento_tipo']) ?>: <?= htmlspecialchars($candidato['documento_numero']) ?>
                </div>
                <?php endif; ?>
                <?php 
                $endereco_completo = [];
                if (!empty($candidato["endereco"])) $endereco_completo[] = $candidato["endereco"];
                if (!empty($candidato["numero"])) $endereco_completo[] = ", nº " . $candidato["numero"];
                if (!empty($candidato["complemento"])) $endereco_completo[] = " (" . $candidato["complemento"] . ")";
                if (!empty($candidato["bairro"])) $endereco_completo[] = " - " . $candidato["bairro"];
                if (!empty($candidato["municipio"])) $endereco_completo[] = " - " . $candidato["municipio"];
                if (!empty($candidato["uf"])) $endereco_completo[] = "/" . $candidato["uf"];
                if (!empty($candidato["cep"])) $endereco_completo[] = " - CEP: " . $candidato["cep"];
                
                if (!empty($endereco_completo)): 
                ?>
                <div class="col-span-2">
                    <strong>Endereço:</strong> <?= htmlspecialchars(implode("", $endereco_completo)) ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="mt-4 p-3 bg-blue-100 rounded">
                <p class="text-blue-800 text-sm">
                    <strong>Importante:</strong> O documento de identidade deve ser o mesmo cadastrado no sistema. 
                    Verifique se seus dados estão corretos antes de prosseguir.
                </p>
            </div>
        </div>

        <!-- Requisitos da Vaga -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Requisitos da Vaga</h3>
            <div class="space-y-3">
                <?php if (!empty($cargo['requisitos_texto'])): ?>
                <div>
                    <strong>Requisitos:</strong>
                    <p class="mt-1 text-gray-700"><?= nl2br(htmlspecialchars($cargo['requisitos_texto'])) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                    <div class="bg-white p-3 rounded border">
                        <strong>Total de Vagas:</strong> <?= $cargo['vagas_total'] ?>
                    </div>
                    <?php if ($cargo['vagas_pcd'] > 0): ?>
                    <div class="bg-purple-50 p-3 rounded border border-purple-200">
                        <strong>Vagas PCD:</strong> <?= $cargo['vagas_pcd'] ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($cargo['vagas_ppp'] > 0): ?>
                    <div class="bg-orange-50 p-3 rounded border border-orange-200">
                        <strong>Vagas PPP:</strong> <?= $cargo['vagas_ppp'] ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($cargo['vagas_reserva']) && $cargo['vagas_reserva'] > 0): ?>
                    <div class="bg-green-50 p-3 rounded border border-green-200">
                        <strong>Vagas Cadastro Reserva:</strong> <?= $cargo['vagas_reserva'] ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($cargo['salario_base'])): ?>
                <div>
                    <strong>Salário:</strong> R$ <?= number_format($cargo['salario_base'], 2, ',', '.') ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($cargo['secretaria'])): ?>
                <div>
                    <strong>Secretaria:</strong> <?= htmlspecialchars($cargo['secretaria']) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($cargo['nivel'])): ?>
                <div>
                    <strong>Nível:</strong> <?= htmlspecialchars($cargo['nivel']) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($cargo['carga_horaria'])): ?>
                <div>
                    <strong>Carga Horária:</strong> <?= htmlspecialchars($cargo['carga_horaria']) ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($cargo['microrregiao'])): ?>
                <div>
                    <strong>Microrregião:</strong> <?= htmlspecialchars(ucfirst($cargo['microrregiao'])) ?>
                </div>
                <?php endif; ?>

                <?php if ($cargo['cadastro_reserva']): ?>
                <div class="bg-green-50 p-3 rounded border border-green-200">
                    <strong>Cadastro de Reserva:</strong> Sim - Esta vaga possui cadastro de reserva
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php 
        // Verificar se há documentos específicos do cargo
        $documentos_cargo = \App\models\PssCargo::buscarDocumentosExigidos($cargo['id']);
        $temDocumentosCargo = !empty($documentos_cargo);
        $totalEtapas = $temDocumentosCargo ? 3 : 2;
        ?>

        <!-- Indicador de Progresso -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex items-center text-green-600">
                        <div class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full text-sm font-bold step-indicator" data-step="1">
                            1
                        </div>
                        <span class="ml-2 text-sm font-medium step-label" data-step="1">Documentos Pessoais</span>
                    </div>
                </div>
                
                <?php if ($temDocumentosCargo): ?>
                    <div class="flex-1 h-1 mx-4 bg-gray-200 rounded">
                        <div class="h-1 bg-green-600 rounded transition-all duration-300" id="progress-bar" style="width: 33.33%"></div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center text-gray-400">
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-600 rounded-full text-sm font-bold step-indicator" data-step="2">
                                2
                            </div>
                            <span class="ml-2 text-sm font-medium step-label" data-step="2">Documentos do Cargo</span>
                        </div>
                    </div>
                    <div class="flex-1 h-1 mx-4 bg-gray-200 rounded">
                        <div class="h-1 bg-gray-200 rounded transition-all duration-300"></div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center text-gray-400">
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-600 rounded-full text-sm font-bold step-indicator" data-step="3">
                                3
                            </div>
                            <span class="ml-2 text-sm font-medium step-label" data-step="3">Títulos</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex-1 h-1 mx-4 bg-gray-200 rounded">
                        <div class="h-1 bg-green-600 rounded transition-all duration-300" id="progress-bar" style="width: 50%"></div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center text-gray-400">
                            <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-600 rounded-full text-sm font-bold step-indicator" data-step="2">
                                2
                            </div>
                            <span class="ml-2 text-sm font-medium step-label" data-step="2">Títulos</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulário de Upload -->
        <form action="/pss/<?= $pss['id'] ?>/cargo/<?= $cargo['id'] ?>/inscricao" method="POST" enctype="multipart/form-data" id="upload-form">
            
            <!-- ETAPA 1: Documentos Pessoais Obrigatórios -->
            <div class="step-content" id="step-1">
                <div class="bg-blue-50 border-l-4 border-blue-400 p-6 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-blue-900">Etapa 1: Documentos Pessoais Obrigatórios</h3>
                            <p class="text-sm text-blue-700 mt-1">Confirme seus dados pessoais e envie seu documento de identidade</p>
                        </div>
                    </div>
                </div>

                <!-- Dados do Candidato -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Seus Dados Cadastrados</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="bg-white p-4 rounded border">
                            <strong class="text-gray-700">Nome Completo:</strong>
                            <p class="text-gray-900 mt-1"><?= htmlspecialchars($candidato['nome']) ?></p>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <strong class="text-gray-700">CPF:</strong>
                            <p class="text-gray-900 mt-1 font-mono"><?= htmlspecialchars($candidato['cpf']) ?></p>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <strong class="text-gray-700">Email:</strong>
                            <p class="text-gray-900 mt-1"><?= htmlspecialchars($candidato['email']) ?></p>
                        </div>
                        <div class="bg-white p-4 rounded border">
                            <strong class="text-gray-700">Telefone:</strong>
                            <p class="text-gray-900 mt-1"><?= htmlspecialchars($candidato['celular'] ?? 'Não informado') ?></p>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-blue-100 rounded">
                        <p class="text-blue-800 text-sm">
                            <strong>Importante:</strong> Verifique se seus dados estão corretos. O documento de identidade deve corresponder aos dados cadastrados.
                        </p>
                    </div>
                </div>

                <!-- Upload do Documento -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="documento_identidade" class="block text-sm font-medium text-gray-700 mb-2">
                                Documento de Identidade *
                            </label>
                            <p class="text-xs text-gray-500 mb-3">
                                Conforme documento cadastrado: <?= htmlspecialchars($candidato['documento_tipo'] ?? 'RG/CNH') ?>: <?= htmlspecialchars($candidato['documento_numero'] ?? '') ?>
                            </p>
                            <input type="file" id="documento_identidade" name="documento_identidade" required
                                   accept=".pdf"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>

                        <!-- Campo de PCD condicional -->
                        <?php if ($cargo["vagas_pcd"] > 0): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Deseja concorrer às vagas para Pessoa com Deficiência (PCD)? *
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="concorrer_pcd" value="nao" checked onchange="toggleLaudoPCD()"
                                           class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Não</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="concorrer_pcd" value="sim" onchange="toggleLaudoPCD()"
                                           class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Sim</span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Campo de Laudo PCD (oculto inicialmente) -->
                    <?php if ($cargo["vagas_pcd"] > 0): ?>
                    <div id="campo-laudo-pcd" style="display: none;" class="mt-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <label for="laudo_pcd" class="block text-sm font-medium text-purple-700 mb-2">
                            Laudo Médico PCD *
                        </label>
                        <p class="text-xs text-purple-600 mb-3">
                            Laudo médico que comprove a deficiência, emitido por profissional habilitado
                        </p>
                        <input type="file" id="laudo_pcd" name="laudo_pcd"
                               accept=".pdf"
                               class="mt-1 block w-full px-3 py-2 border border-purple-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    </div>
                    <?php endif; ?>

                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-yellow-800 text-sm">
                            <strong>Formato:</strong> Apenas arquivos PDF são aceitos. Tamanho máximo: 10MB por arquivo.
                        </p>
                    </div>
                </div>

                <!-- Botões de Navegação -->
                <div class="flex justify-between">
                    <button type="button" id="btn-anterior" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors" onclick="anteriorEtapa()" style="display: none;">
                        ← Anterior
                    </button>
                    <button type="button" id="btn-proximo" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors ml-auto" onclick="proximaEtapa()">
                        Próximo →
                    </button>
                    <button type="submit" id="btn-finalizar" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors ml-auto" style="display: none;">
                        Finalizar Inscrição
                    </button>
                </div>
            </div>

            <!-- ETAPA 2: Documentos Específicos do Cargo (se existirem) -->
            <?php if ($temDocumentosCargo): ?>
            <div class="step-content hidden" id="step-2">
                <div class="bg-orange-50 border-l-4 border-orange-400 p-6 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-orange-900">Etapa 2: Documentos Específicos do Cargo</h3>
                            <p class="text-sm text-orange-700 mt-1">Documentos exigidos especificamente para este cargo</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <div class="space-y-6">
                        <?php foreach ($documentos_cargo as $doc_cargo): ?>
                        <div>
                            <label for="<?= htmlspecialchars($doc_cargo['tipo_documento_codigo']) ?>" class="block text-sm font-medium text-gray-700 mb-2">
                                <?= htmlspecialchars($doc_cargo['tipo_documento_nome']) ?> <?= $doc_cargo['obrigatorio'] ? '*' : '(Opcional)' ?>
                            </label>
                            <?php if (!empty($doc_cargo['descricao'])): ?>
                            <p class="text-xs text-gray-500 mb-3">
                                <?= htmlspecialchars($doc_cargo['descricao']) ?>
                            </p>
                            <?php endif; ?>
                            <input type="file" 
                                   id="<?= htmlspecialchars($doc_cargo['tipo_documento_codigo']) ?>" 
                                   name="<?= htmlspecialchars($doc_cargo['tipo_documento_codigo']) ?><?= $doc_cargo['multiplos_arquivos'] ? '[]' : '' ?>"
                                   <?= $doc_cargo['obrigatorio'] ? 'required' : '' ?>
                                   <?= $doc_cargo['multiplos_arquivos'] ? 'multiple' : '' ?>
                                   accept=".pdf"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-yellow-800 text-sm">
                            <strong>Formato:</strong> Apenas arquivos PDF são aceitos. Tamanho máximo: 10MB por arquivo.
                        </p>
                    </div>
                </div>

                <!-- Botões de Navegação -->
                <div class="flex justify-between">
                    <button type="button" id="btn-anterior" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors" onclick="anteriorEtapa()">
                        ← Anterior
                    </button>
                    <button type="button" id="btn-proximo" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors" onclick="proximaEtapa()">
                        Próximo →
                    </button>
                    <button type="submit" id="btn-finalizar" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" style="display: none;">
                        Finalizar Inscrição
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- ETAPA FINAL: Títulos e Experiência -->
            <div class="step-content hidden" id="step-<?= $temDocumentosCargo ? '3' : '2' ?>">
                <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-green-900">Etapa <?= $temDocumentosCargo ? '3' : '2' ?>: Títulos e Experiência</h3>
                            <p class="text-sm text-green-700 mt-1">Documentos que comprovem sua experiência e qualificações</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="comprovante_escolaridade" class="block text-sm font-medium text-gray-700 mb-2">
                                Comprovante de Escolaridade *
                            </label>
                            <p class="text-xs text-gray-500 mb-3">
                                Conforme requisito. Declaração emitida por instituição de ensino reconhecida pelo MEC e histórico escolar ou Certificado.
                            </p>
                            <input type="file" id="comprovante_escolaridade" name="comprovante_escolaridade" required
                                   accept=".pdf"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="comprovante_experiencia_declaracoes" class="block text-sm font-medium text-gray-700 mb-2">
                                Comprovante de Experiência ou Declarações (Opcional)
                            </label>
                            <p class="text-xs text-gray-500 mb-3">
                                Carteira de trabalho, declarações ou outros documentos que comprovem experiência na área (múltiplos arquivos permitidos)
                            </p>
                            <input type="file" id="comprovante_experiencia_declaracoes" name="comprovante_experiencia_declaracoes[]"
                                   accept=".pdf" multiple
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="certificados" class="block text-sm font-medium text-gray-700 mb-2">
                            Certificados (Opcional)
                        </label>
                        <p class="text-xs text-gray-500 mb-3">
                            Certificados de cursos, especializações ou qualificações adicionais (múltiplos arquivos permitidos)
                        </p>
                        <input type="file" id="certificados" name="certificados[]" multiple
                               accept=".pdf"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>

                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <p class="text-yellow-800 text-sm">
                            <strong>Formato:</strong> Apenas arquivos PDF são aceitos. Tamanho máximo: 10MB por arquivo.
                        </p>
                    </div>
                </div>

                <!-- Declaração -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <input type="checkbox" id="declaracao" name="declaracao" required
                               class="mt-1 focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
                        <div class="ml-3">
                            <label for="declaracao" class="text-sm font-medium text-red-900">
                                Declaro que: *
                            </label>
                            <p class="text-sm text-red-700 mt-1">
                                Todas as informações prestadas são verdadeiras e estou ciente de que a falsidade de qualquer dado pode acarretar na eliminação do processo seletivo, além das sanções legais cabíveis. Declaro também que preencho todos os requisitos exigidos para o cargo e que os documentos enviados são autênticos.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botões de Navegação -->
                <div class="flex justify-between">
                    <button type="button" id="btn-anterior" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors" onclick="anteriorEtapa()">
                        ← Anterior
                    </button>
                    <button type="button" id="btn-proximo" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors" onclick="proximaEtapa()" style="display: none;">
                        Próximo →
                    </button>
                    <button type="submit" id="btn-finalizar" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                        Finalizar Inscrição
                    </button>
                </div>
            </div>

        </form>

        <!-- Mensagens de Erro -->
        <?php if (isset($_SESSION['erro_inscricao'])): ?>
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800"><?= htmlspecialchars($_SESSION['erro_inscricao']) ?></p>
        </div>
        <?php unset($_SESSION['erro_inscricao']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['erro'])): ?>
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800"><?= htmlspecialchars($_GET['erro']) ?></p>
        </div>
        <?php endif; ?>

    </div>
</section>

<style>
.step-indicator.active {
    background-color: #059669 !important;
    color: white !important;
}

.step-indicator.completed {
    background-color: #10b981 !important;
    color: white !important;
}

.step-label.active {
    color: #059669 !important;
    font-weight: 600 !important;
}

.step-label.completed {
    color: #10b981 !important;
}

.step-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
let etapaAtual = 1;
const totalEtapas = <?= $totalEtapas ?>;

// Função para mostrar/ocultar campo de laudo PCD
function toggleLaudoPCD() {
    const concorrerPCD = document.querySelector('input[name="concorrer_pcd"]:checked').value;
    const campoLaudo = document.getElementById('campo-laudo-pcd');
    const inputLaudo = document.getElementById('laudo_pcd');
    
    if (concorrerPCD === 'sim') {
        campoLaudo.style.display = 'block';
        inputLaudo.required = true;
    } else {
        campoLaudo.style.display = 'none';
        inputLaudo.required = false;
        inputLaudo.value = '';
    }
}

// Função para validar etapa atual
function validarEtapa(etapa) {
    let valido = true;
    let mensagem = '';
    
    if (etapa === 1) {
        const docIdentidade = document.getElementById('documento_identidade');
        if (!docIdentidade.files.length) {
            valido = false;
            mensagem = 'É obrigatório enviar o documento de identidade.';
        }
        
        const concorrerPCD = document.querySelector('input[name="concorrer_pcd"]:checked');
        const inputLaudo = document.getElementById('laudo_pcd');
        if (concorrerPCD && concorrerPCD.value === 'sim' && (!inputLaudo.files.length)) {
            valido = false;
            mensagem = 'É obrigatório enviar o laudo médico para concorrer às vagas PCD.';
        }
    } else if (etapa === 2 && totalEtapas === 3) {
        // Validar documentos específicos do cargo (apenas se existirem)
        const documentosObrigatorios = document.querySelectorAll('#step-2 input[type="file"][required]');
        for (let input of documentosObrigatorios) {
            if (!input.files.length) {
                valido = false;
                mensagem = 'Todos os documentos específicos do cargo são obrigatórios.';
                break;
            }
        }
    } else if ((etapa === 3 && totalEtapas === 3) || (etapa === 2 && totalEtapas === 2)) {
        const escolaridade = document.getElementById('comprovante_escolaridade');
        const declaracao = document.getElementById('declaracao');
        
        if (!escolaridade.files.length) {
            valido = false;
            mensagem = 'É obrigatório enviar o comprovante de escolaridade.';
        } else if (!declaracao.checked) {
            valido = false;
            mensagem = 'É obrigatório aceitar a declaração.';
        }
    }
    
    if (!valido) {
        alert(mensagem);
    }
    
    return valido;
}

// Função para ir para próxima etapa
function proximaEtapa() {
    if (!validarEtapa(etapaAtual)) {
        return;
    }
    
    if (etapaAtual < totalEtapas) {
        etapaAtual++;
        
        // Se não há documentos do cargo e estamos na etapa 1, pular para a etapa de títulos
        if (totalEtapas === 2 && etapaAtual === 2) {
            // Já está na etapa correta (títulos)
        }
        
        atualizarEtapa();
    }
}

// Função para voltar etapa
function anteriorEtapa() {
    if (etapaAtual > 1) {
        etapaAtual--;
        atualizarEtapa();
    }
}

// Função para atualizar a interface da etapa
function atualizarEtapa() {
    // Ocultar todas as etapas
    document.querySelectorAll('.step-content').forEach(step => {
        step.classList.add('hidden');
    });
    
    // Determinar o ID correto da etapa
    let stepId;
    if (totalEtapas === 2) {
        // Sem documentos do cargo: etapa 1 = pessoais, etapa 2 = títulos
        stepId = etapaAtual;
    } else {
        // Com documentos do cargo: etapa 1 = pessoais, etapa 2 = cargo, etapa 3 = títulos
        stepId = etapaAtual;
    }
    
    // Mostrar etapa atual
    const stepElement = document.getElementById(`step-${stepId}`);
    if (stepElement) {
        stepElement.classList.remove('hidden');
    }
    
    // Atualizar indicadores
    for (let i = 1; i <= totalEtapas; i++) {
        const indicator = document.querySelector(`.step-indicator[data-step="${i}"]`);
        const label = document.querySelector(`.step-label[data-step="${i}"]`);
        
        indicator.classList.remove('active', 'completed');
        label.classList.remove('active', 'completed');
        
        if (i < etapaAtual) {
            indicator.classList.add('completed');
            label.classList.add('completed');
        } else if (i === etapaAtual) {
            indicator.classList.add('active');
            label.classList.add('active');
        }
    }
    
    // Atualizar barra de progresso
    const progresso = (etapaAtual / totalEtapas) * 100;
    document.getElementById('progress-bar').style.width = `${progresso}%`;
    
    // Atualizar botões
    const btnAnterior = document.getElementById('btn-anterior');
    const btnProximo = document.getElementById('btn-proximo');
    const btnFinalizar = document.getElementById('btn-finalizar');
    
    if (etapaAtual === 1) {
        btnAnterior.style.display = 'none';
        btnProximo.style.display = 'inline-block';
        btnFinalizar.style.display = 'none';
    } else if (etapaAtual === totalEtapas) {
        btnAnterior.style.display = 'inline-block';
        btnProximo.style.display = 'none';
        btnFinalizar.style.display = 'inline-block';
    } else {
        btnAnterior.style.display = 'inline-block';
        btnProximo.style.display = 'inline-block';
        btnFinalizar.style.display = 'none';
    }
}

// Validação de tamanho de arquivo
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (this.files.length > 0) {
            for (let file of this.files) {
                if (file.size > maxSize) {
                    alert('O arquivo "' + file.name + '" excede o tamanho máximo de 10MB.');
                    this.value = '';
                    return;
                }
                
                // Verificar se é PDF
                if (!file.type.includes('pdf')) {
                    alert('O arquivo "' + file.name + '" deve estar em formato PDF.');
                    this.value = '';
                    return;
                }
            }
        }
    });
});

// Validação final do formulário
document.getElementById('upload-form').addEventListener('submit', function(e) {
    let todasEtapasValidas = true;
    
    // Validar etapa 1 (sempre existe)
    if (!validarEtapa(1)) {
        todasEtapasValidas = false;
    }
    
    // Validar etapa 2 se for de documentos do cargo
    if (totalEtapas === 3 && !validarEtapa(2)) {
        todasEtapasValidas = false;
    }
    
    // Validar etapa final (títulos)
    const etapaFinal = totalEtapas === 3 ? 3 : 2;
    if (!validarEtapa(etapaFinal)) {
        todasEtapasValidas = false;
    }
    
    if (!todasEtapasValidas) {
        e.preventDefault();
        return false;
    }
});

// Inicializar a primeira etapa
document.addEventListener('DOMContentLoaded', function() {
    atualizarEtapa();
});
</script>
