<?php $this->layout("template") ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);">
    <div class="max-w-4xl w-full space-y-8 bg-white rounded-lg shadow-xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Cadastro de Candidato</h2>
            <p class="text-gray-600">Preencha seus dados para criar sua conta e se inscrever nos processos seletivos.</p>
        </div>

        <?php if (isset($_SESSION['erro_cadastro'])): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800"><?= htmlspecialchars($_SESSION['erro_cadastro']) ?></p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['erro_cadastro']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensagem_login'])): ?>
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-800"><?= htmlspecialchars($_SESSION['mensagem_login']) ?></p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['mensagem_login']); ?>
        <?php endif; ?>

        <form method="POST" action="/cadastro" class="space-y-6" id="formCadastro">
            <!-- Dados Pessoais -->
            <fieldset class="border border-gray-200 rounded-md p-6">
                <legend class="text-lg font-semibold text-gray-900 px-2">Dados Pessoais</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo: <span class="text-red-600">*</span></label>
                        <input type="text" id="nome" name="nome" autocomplete="off" required
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['nome'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="nome_social" class="block text-sm font-medium text-gray-700">Nome Social (opcional):</label>
                        <input type="text" id="nome_social" name="nome_social" autocomplete="off"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['nome_social'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            placeholder="Nome pelo qual você gostaria de ser chamado(a)">
                        <p class="mt-1 text-xs text-gray-500">O nome social aparecerá antes do nome civil nos documentos do processo, separado por hífen.</p>
                    </div>
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF: <span class="text-red-600">*</span></label>
                        <input type="text" id="cpf" name="cpf" autocomplete="off" maxlength="14"
                            pattern="\d{3}\.?\d{3}\.?\d{3}-?\d{2}" title="Digite o CPF no formato: XXX.XXX.XXX-XX"
                            oninput="mascaraCPF(this)" required
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['cpf'] ?? $_SESSION['cpf_login'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="data_nascimento" class="block text-sm font-medium text-gray-700">Data de Nascimento: <span class="text-red-600">*</span></label>
                        <input type="text" id="data_nascimento" name="data_nascimento" autocomplete="off" required
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['data_nascimento'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" placeholder="dd/mm/yyyy" oninput="mascaraData(this)">
                    </div>
                    <div>
                        <label for="genero_id" class="block text-sm font-medium text-gray-700">Gênero: <span class="text-red-600">*</span></label>
                        <select name="genero_id" id="genero_id" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Selecione uma opção</option>
                            <?php if (isset($generos) && is_array($generos) && count($generos)): ?>
                                <?php foreach ($generos as $genero): ?>
                                    <option value="<?= htmlspecialchars($genero->id ?? '') ?>" 
                                        <?= (($_SESSION['dados_formulario']['genero_id'] ?? '') == ($genero->id ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($genero->nome ?? $genero->genero ?? 'Opção não disponível') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email: <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" autocomplete="off" required
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['email'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700">Senha: <span class="text-red-600">*</span></label>
                        <input type="password" id="senha" name="senha" autocomplete="new-password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            onkeyup="validarSenhas()">
                        <p class="mt-1 text-xs text-gray-500">Mínimo 6 caracteres</p>
                    </div>
                    <div>
                        <label for="confirmar_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha: <span class="text-red-600">*</span></label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" autocomplete="new-password" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            onkeyup="validarSenhas()">
                        <div id="senha-feedback" class="mt-1 text-xs hidden">
                            <span id="senha-match" class="text-green-600 hidden">✓ Senhas coincidem</span>
                            <span id="senha-nomatch" class="text-red-600 hidden">✗ Senhas não coincidem</span>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Documento de Identificação -->
            <fieldset class="border border-gray-200 rounded-md p-6">
                <legend class="text-lg font-semibold text-gray-900 px-2">Documento de Identificação</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="documento_tipo" class="block text-sm font-medium text-gray-700">Tipo Documento:</label>
                        <select name="documento_tipo" id="documento_tipo" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="RG" <?= (($_SESSION['dados_formulario']['documento_tipo'] ?? 'RG') == 'RG') ? 'selected' : '' ?>>RG</option>
                            <option value="CNH" <?= (($_SESSION['dados_formulario']['documento_tipo'] ?? '') == 'CNH') ? 'selected' : '' ?>>CNH</option>
                            <option value="Passaporte" <?= (($_SESSION['dados_formulario']['documento_tipo'] ?? '') == 'Passaporte') ? 'selected' : '' ?>>Passaporte</option>
                            <option value="Documento_Classe" <?= (($_SESSION['dados_formulario']['documento_tipo'] ?? '') == 'Documento_Classe') ? 'selected' : '' ?>>Documento de Classe</option>
                        </select>
                    </div>
                    <div>
                        <label for="documento_numero" class="block text-sm font-medium text-gray-700">Número:</label>
                        <input type="text" id="documento_numero" name="documento_numero" autocomplete="off"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['documento_numero'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="documento_orgao" class="block text-sm font-medium text-gray-700">Órgão Emissor:</label>
                        <input type="text" id="documento_orgao" name="documento_orgao" autocomplete="off"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['documento_orgao'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="documento_uf_id" class="block text-sm font-medium text-gray-700">UF:</label>
                        <select name="documento_uf_id" id="documento_uf_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Selecione o estado</option>
                            <?php if (isset($estados) && is_array($estados) && count($estados)): ?>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= htmlspecialchars($estado->id ?? '') ?>"
                                        <?= (($_SESSION['dados_formulario']['documento_uf_id'] ?? '') == ($estado->id ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($estado->estado ?? $estado->nome ?? 'Estado não disponível') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Endereço -->
            <fieldset class="border border-gray-200 rounded-md p-6">
                <legend class="text-lg font-semibold text-gray-900 px-2">Endereço</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="cep" class="block text-sm font-medium text-gray-700">CEP:</label>
                        <input type="text" id="cep" name="cep" autocomplete="off" maxlength="9" oninput="mascaraCEP(this)" onblur="buscarCEP(this.value)"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['cep'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="endereco" class="block text-sm font-medium text-gray-700">Logradouro (Rua, Avenida, Alameda, etc):</label>
                        <input type="text" id="endereco" name="endereco" autocomplete="off"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['endereco'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700">Número:</label>
                        <input type="text" id="numero" name="numero" autocomplete="off"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['numero'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="complemento" class="block text-sm font-medium text-gray-700">Complemento (opcional):</label>
                        <input type="text" id="complemento" name="complemento" autocomplete="off"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['complemento'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            placeholder="Apto, bloco, casa, etc.">
                    </div>
                    <div>
                        <label for="bairro" class="block text-sm font-medium text-gray-700">Bairro: <span class="text-red-600">*</span></label>
                        <input type="text" id="bairro" name="bairro" autocomplete="off" required
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['bairro'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="estado_id" class="block text-sm font-medium text-gray-700">Estado:</label>
                      <select name="estado_id" id="estado_id" onchange="carregarCidades(this.value, 'municipio_id')" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">                          <option value="" disabled selected>Selecione o estado</option>
                            <?php if (isset($estados) && is_array($estados) && count($estados)): ?>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= htmlspecialchars($estado->id ?? '') ?>"
                                        <?= (($_SESSION['dados_formulario']['estado_id'] ?? '') == ($estado->id ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($estado->estado ?? $estado->nome ?? 'Estado não disponível') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="municipio_id" class="block text-sm font-medium text-gray-700">Município:</label>
                        <select name="municipio_id" id="municipio_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Primeiro selecione o estado</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <!-- Contato -->
            <fieldset class="border border-gray-200 rounded-md p-6">
                <legend class="text-lg font-semibold text-gray-900 px-2">Contato</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="celular" class="block text-sm font-medium text-gray-700">Celular: <span class="text-red-600">*</span></label>
                        <input type="text" id="celular" name="celular" autocomplete="off" required maxlength="15" oninput="mascaraTelefone(this)"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['celular'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone (Opcional):</label>
                        <input type="text" id="telefone" name="telefone" autocomplete="off" maxlength="14" oninput="mascaraTelefone(this)"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['telefone'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>
            </fieldset>

            <!-- Informações Adicionais -->
            <fieldset class="border border-gray-200 rounded-md p-6">
                <legend class="text-lg font-semibold text-gray-900 px-2">Informações Adicionais</legend>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label for="escolaridade_id" class="block text-sm font-medium text-gray-700">Escolaridade:</label>
                        <select name="escolaridade_id" id="escolaridade_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Selecione a escolaridade</option>
                            <?php if (isset($escolaridades) && is_array($escolaridades) && count($escolaridades)): ?>
                                <?php foreach ($escolaridades as $escolaridade): ?>
                                    <option value="<?= htmlspecialchars($escolaridade->id ?? '') ?>"
                                        <?= (($_SESSION['dados_formulario']['escolaridade_id'] ?? '') == ($escolaridade->id ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($escolaridade->escolaridade ?? $escolaridade->nome ?? 'Escolaridade não disponível') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="nacionalidade_id" class="block text-sm font-medium text-gray-700">Nacionalidade:</label>
                        <select name="nacionalidade_id" id="nacionalidade_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Selecione a nacionalidade</option>
                            <?php if (isset($nacionalidades) && is_array($nacionalidades) && count($nacionalidades)): ?>
                                <?php foreach ($nacionalidades as $nacionalidade): ?>
                                    <option value="<?= htmlspecialchars($nacionalidade->id ?? '') ?>"
                                        <?= (($_SESSION['dados_formulario']['nacionalidade_id'] ?? '') == ($nacionalidade->id ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($nacionalidade->pais ?? $nacionalidade->nacionalidade ?? $nacionalidade->nome ?? 'Nacionalidade não disponível') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="naturalidade_estado_id" class="block text-sm font-medium text-gray-700">Estado de Naturalidade:</label>
                        <select name="naturalidade_estado_id" id="naturalidade_estado_id" onchange="carregarCidades(this.value, 'naturalidade_cidade_id')" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Selecione o estado</option>
                            <?php if (isset($estados) && is_array($estados) && count($estados)): ?>
                                <?php foreach ($estados as $estado): ?>
                                    <option value="<?= htmlspecialchars($estado->id ?? '') ?>"
                                        <?= (($_SESSION['dados_formulario']['naturalidade_estado_id'] ?? '') == ($estado->id ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($estado->estado ?? $estado->nome ?? 'Estado não disponível') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="naturalidade_cidade_id" class="block text-sm font-medium text-gray-700">Cidade de Naturalidade:</label>
                        <select name="naturalidade_cidade_id" id="naturalidade_cidade_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Primeiro selecione o estado</option>
                            <?php if (isset($naturalidade) && is_array($naturalidade) && count($naturalidade)): ?>
                                <?php foreach ($naturalidade as $cidade): ?>
                                    <option value="<?= htmlspecialchars($cidade->id ?? '') ?>"
                                        <?= (($_SESSION['dados_formulario']['naturalidade_cidade_id'] ?? '') == ($cidade->id ?? '')) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cidade->cidade ?? $cidade->nome ?? 'Cidade não disponível') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="nome_mae" class="block text-sm font-medium text-gray-700">Nome da Mãe:</label>
                        <input type="text" id="nome_mae" name="nome_mae" autocomplete="off"
                            value="<?= htmlspecialchars($_SESSION['dados_formulario']['nome_mae'] ?? '') ?>"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>
                </div>

                <!-- PCD e PPP -->
                <div class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pessoa com Deficiência (PCD):</label>
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="pcd" value="0" class="form-radio text-green-600" 
                                        <?= (($_SESSION['dados_formulario']['pcd'] ?? '0') == '0') ? 'checked' : '' ?>>
                                    <span class="ml-2">Não</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="pcd" value="1" class="form-radio text-green-600"
                                        <?= (($_SESSION['dados_formulario']['pcd'] ?? '') == '1') ? 'checked' : '' ?>>
                                    <span class="ml-2">Sim</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Preto, Pardo ou Pessoa Indígena (PPP):</label>
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="ppp" value="0" class="form-radio text-green-600"
                                        <?= (($_SESSION['dados_formulario']['ppp'] ?? '0') == '0') ? 'checked' : '' ?>>
                                    <span class="ml-2">Não</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="ppp" value="1" class="form-radio text-green-600"
                                        <?= (($_SESSION['dados_formulario']['ppp'] ?? '') == '1') ? 'checked' : '' ?>>
                                    <span class="ml-2">Sim</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Termos e Condições -->
            <fieldset class="border border-gray-200 rounded-md p-6">
                <legend class="text-lg font-semibold text-gray-900 px-2">Termos e Condições</legend>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" name="aceito_lgpd" value="1" required 
                            class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label class="text-sm text-gray-700">
                            <span class="text-red-600">*</span> Aceito os termos da Lei Geral de Proteção de Dados (LGPD) e autorizo o tratamento dos meus dados pessoais para fins do processo seletivo.
                        </label>
                    </div>
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" name="aceito_comunicacoes" value="1" 
                            class="mt-1 h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label class="text-sm text-gray-700">
                            Aceito receber comunicações e publicidade da Prefeitura de Parauapebas por email e outros meios.
                        </label>
                    </div>
                </div>
            </fieldset>

            <div class="flex items-center justify-between">
                <a href="/login" class="text-green-600 hover:text-green-500 text-sm font-medium">
                    Já tenho conta
                </a>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Criar Conta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Máscaras de entrada
    function mascaraCPF(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = value;
    }

    function mascaraCEP(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        input.value = value;
    }

    function mascaraTelefone(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        input.value = value;
    }

    function mascaraData(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/(\d{2})(\d)/, '$1/$2');
        value = value.replace(/(\d{2})(\d)/, '$1/$2');
        input.value = value;
    }

    // Validação de senhas
    function validarSenhas() {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;
        const feedback = document.getElementById('senha-feedback');
        const matchElement = document.getElementById('senha-match');
        const nomatchElement = document.getElementById('senha-nomatch');
        
        if (confirmarSenha === '') {
            feedback.classList.add('hidden');
            matchElement.classList.add('hidden');
            nomatchElement.classList.add('hidden');
        } else if (senha === confirmarSenha) {
            feedback.classList.remove('hidden');
            matchElement.classList.remove('hidden');
            nomatchElement.classList.add('hidden');
        } else {
            feedback.classList.remove('hidden');
            matchElement.classList.add('hidden');
            nomatchElement.classList.remove('hidden');
        }
    }

    // Busca CEP via ViaCEP
    function buscarCEP(cep) {
        cep = cep.replace(/\D/g, '');
        
        if (cep.length !== 8) {
            return;
        }

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('endereco').value = data.logradouro || '';
                    document.getElementById('bairro').value = data.bairro || '';
                    
                    // Buscar estado e cidade
                    if (data.uf) {
                        const estadoSelect = document.getElementById('estado_id');
                        for (let option of estadoSelect.options) {
                            if (option.text.includes(data.uf)) {
                                option.selected = true;
                                carregarCidadesEndereco(option.value, data.localidade);
                                break;
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.log('Erro ao buscar CEP:', error);
            });
    }

    // ✅ CORREÇÃO: Carregamento robusto de cidades para endereço
    function carregarCidadesEndereco(estadoId, cidadeSelecionada = null) {
        const selectCidade = document.getElementById('municipio_id');
        
        if (!estadoId) {
            selectCidade.innerHTML = '<option value="" disabled selected>Primeiro selecione o estado</option>';
            return;
        }

        selectCidade.innerHTML = '<option value="" disabled selected>Carregando cidades...</option>';

        // ✅ CORREÇÃO: Múltiplas rotas de fallback
        const rotas = [
            `/api/estados/${estadoId}/cidades`,
            `/painel/api/estados/${estadoId}/cidades`
        ];

        async function tentarCarregarCidades() {
            for (let i = 0; i < rotas.length; i++) {
                try {
                    console.log(`Tentando rota ${i + 1}: ${rotas[i]}`);
                    
                    const response = await fetch(rotas[i]);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data && Array.isArray(data) && data.length > 0) {
                        console.log(`Sucesso na rota ${i + 1}:`, data);
                        
                        selectCidade.innerHTML = '<option value="" disabled selected>Selecione a cidade</option>';
                        
                        data.forEach(cidade => {
                            const option = document.createElement('option');
                            option.value = cidade.id;
                            option.textContent = cidade.cidade || cidade.nome || cidade.municipio || 'Cidade sem nome';
                            
                            if (cidadeSelecionada && (
                                cidade.cidade === cidadeSelecionada || 
                                cidade.nome === cidadeSelecionada ||
                                cidade.municipio === cidadeSelecionada
                            )) {
                                option.selected = true;
                            }
                            
                            selectCidade.appendChild(option);
                        });
                        
                        return; // Sucesso, sair do loop
                    } else {
                        throw new Error('Dados vazios ou inválidos');
                    }
                } catch (error) {
                    console.log(`Erro na rota ${i + 1} (${rotas[i]}):`, error.message);
                    
                    if (i === rotas.length - 1) {
                        // Última tentativa falhou
                        selectCidade.innerHTML = '<option value="" disabled>Erro ao carregar cidades</option>';
                        console.error('Todas as rotas falharam para carregar cidades');
                    }
                }
            }
        }

        tentarCarregarCidades();
    }

    // ✅ CORREÇÃO: Carregamento robusto de cidades para naturalidade
    function carregarCidadesNaturalidade(estadoId) {
        const selectCidade = document.getElementById('naturalidade_cidade_id');
        
        if (!estadoId) {
            selectCidade.innerHTML = '<option value="" disabled selected>Primeiro selecione o estado</option>';
            return;
        }

        selectCidade.innerHTML = '<option value="" disabled selected>Carregando cidades...</option>';

        // ✅ CORREÇÃO: Múltiplas rotas de fallback
        const rotas = [
            `/api/estados/${estadoId}/cidades`,
            `/painel/api/estados/${estadoId}/cidades`
        ];

        async function tentarCarregarCidades() {
            for (let i = 0; i < rotas.length; i++) {
                try {
                    console.log(`Tentando rota ${i + 1}: ${rotas[i]}`);
                    
                    const response = await fetch(rotas[i]);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data && Array.isArray(data) && data.length > 0) {
                        console.log(`Sucesso na rota ${i + 1}:`, data);
                        
                        selectCidade.innerHTML = '<option value="" disabled selected>Selecione a cidade</option>';
                        
                        data.forEach(cidade => {
                            const option = document.createElement('option');
                            option.value = cidade.id;
                            option.textContent = cidade.cidade || cidade.nome || cidade.municipio || 'Cidade sem nome';
                            selectCidade.appendChild(option);
                        });
                        
                        return; // Sucesso, sair do loop
                    } else {
                        throw new Error('Dados vazios ou inválidos');
                    }
                } catch (error) {
                    console.log(`Erro na rota ${i + 1} (${rotas[i]}):`, error.message);
                    
                    if (i === rotas.length - 1) {
                        // Última tentativa falhou
                        selectCidade.innerHTML = '<option value="" disabled>Erro ao carregar cidades</option>';
                        console.error('Todas as rotas falharam para carregar cidades');
                    }
                }
            }
        }

        tentarCarregarCidades();
    }

    // Validação do formulário
    document.getElementById('formCadastro').addEventListener('submit', function(e) {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;
        
        if (senha !== confirmarSenha) {
            e.preventDefault();
            alert('As senhas não coincidem. Por favor, verifique.');
            return false;
        }
        
        if (senha.length < 6) {
            e.preventDefault();
            alert('A senha deve ter pelo menos 6 caracteres.');
            return false;
        }
        
        const lgpd = document.querySelector('input[name="aceito_lgpd"]:checked');
        if (!lgpd) {
            e.preventDefault();
            alert('Você deve aceitar os termos da LGPD para continuar.');
            return false;
        }
    });

    // Carregar cidades se estado já estiver selecionado (para preservar dados em caso de erro)
    document.addEventListener('DOMContentLoaded', function() {
        const estadoEndereco = document.getElementById('estado_id').value;
        if (estadoEndereco) {
            carregarCidadesEndereco(estadoEndereco);
        }
        
        const estadoNaturalidade = document.getElementById('naturalidade_estado_id').value;
        if (estadoNaturalidade) {
            carregarCidadesNaturalidade(estadoNaturalidade);
        }
    });
</script>


<script>
    function carregarCidades(estadoId, cidadeSelectId) {
        const selectCidade = document.getElementById(cidadeSelectId);
        if (!estadoId) {
            selectCidade.innerHTML = '<option value="" disabled selected>Primeiro selecione o estado</option>';
            return;
        }

        selectCidade.innerHTML = '<option value="" disabled selected>Carregando cidades...</option>';

        fetch(`/api/cidades-por-estado/${estadoId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                selectCidade.innerHTML = '<option value="" disabled selected>Selecione a cidade</option>';
                if (data && Array.isArray(data)) {
                    data.forEach(cidade => {
                        const option = document.createElement('option');
                        option.value = cidade.id;
                        option.textContent = cidade.cidade;
                        selectCidade.appendChild(option);
                    });
                } else {
                    throw new Error('Formato de dados inválido');
                }
            })
            .catch(error => {
                console.error('Erro ao carregar cidades:', error);
                selectCidade.innerHTML = '<option value="" disabled>Erro ao carregar cidades</option>';
            });
    }
</script>


