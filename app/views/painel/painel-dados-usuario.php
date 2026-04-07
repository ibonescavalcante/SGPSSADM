<?php
// Verificar se o usuário está logado
if (!isset($_SESSION["usuario"])) {
    header("Location: /login");
    exit;
}

// Dados padrão se não foram passados
$candidato = $candidato ?? [];
$generos = $generos ?? [];
$escolaridades = $escolaridades ?? [];
$estados = $estados ?? [];
$pode_editar = $pode_editar ?? true;
$mensagem = $mensagem ?? "";
$tipo_mensagem = $tipo_mensagem ?? "";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Dados - PSS</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="bg-gray-100 min-h-screen py-8">
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg">
            <!-- Header -->
            <div class="bg-green-900 text-white p-6 rounded-t-lg">
                <h1 class="text-2xl font-bold">MEUS DADOS</h1>
                <p class="text-green-200">Mantenha suas informações sempre atualizadas</p>
            </div>

            <!-- Mensagens -->
            <?php if (!empty($mensagem)) : ?>
            <div class="p-4 m-4 rounded <?= $tipo_mensagem === 'sucesso' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' ?>">
                <i class="fas <?= $tipo_mensagem === 'sucesso' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                <?= htmlspecialchars($mensagem) ?>
            </div>
            <?php endif; ?>

            <!-- Mensagem de Restrição de Edição -->
            <?php if (!$pode_editar && !empty($mensagem_restricao ?? '')) : ?>
            <div class="p-4 m-4 rounded bg-yellow-100 text-yellow-800 border border-yellow-300">
                <i class="fas fa-lock mr-2"></i>
                <strong>Edição Restrita:</strong> <?= htmlspecialchars($mensagem_restricao) ?>
            </div>
            <?php endif; ?>

            <!-- Formulário -->
            <div class="p-6">
                <form method="POST" class="space-y-8" id="formDados">
                    
                    <!-- DADOS PESSOAIS -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-bold mb-4 bg-gray-100 p-3 text-center">DADOS PESSOAIS</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nome -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2">Nome Completo *</label>
                                <input type="text" name="nome" required
                                    value="<?= htmlspecialchars($candidato['nome'] ?? '') ?>"
                                    <?= !$pode_editar ? 'disabled' : '' ?>
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500 <?= !$pode_editar ? 'bg-gray-100' : '' ?>">
                            </div>

                            <!-- Nome Social -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2">Nome Social</label>
                                <input type="text" name="nome_social"
                                    value="<?= htmlspecialchars($candidato['nome_social'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>

                            <!-- CPF -->
                            <div>
                                <label class="block text-sm font-medium mb-2">CPF *</label>
                                <input type="text" name="cpf" disabled
                                    value="<?= htmlspecialchars($candidato['cpf'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded bg-gray-100">
                                <small class="text-gray-600">CPF não pode ser alterado</small>
                            </div>

                            <!-- Data de Nascimento -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Data de Nascimento *</label>
                                <input type="date" name="data_nascimento" required
                                    value="<?= htmlspecialchars($candidato['data_nascimento'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>

                            <!-- Gênero -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Gênero *</label>
                                <select name="genero_id" required
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($generos as $genero) : ?>
                                    <option value="<?= $genero['id'] ?>" 
                                        <?= ($candidato['genero_id'] ?? '') == $genero['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($genero['nome']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Email *</label>
                                <input type="email" name="email" required
                                    value="<?= htmlspecialchars($candidato['email'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>

                            <!-- Nome da Mãe -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2">Nome da Mãe</label>
                                <input type="text" name="nome_mae"
                                    value="<?= htmlspecialchars($candidato['nome_mae'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>
                        </div>
                    </div>

                    <!-- DADOS ADICIONAIS -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-bold mb-4 bg-gray-100 p-3 text-center">DADOS ADICIONAIS</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Escolaridade -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-2">Escolaridade</label>
                                <select name="escolaridade_id"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($escolaridades as $escolaridade) : ?>
                                    <option value="<?= $escolaridade['id'] ?>" 
                                        <?= ($candidato['escolaridade_id'] ?? '') == $escolaridade['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($escolaridade['escolaridade']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Estado de Naturalidade -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Estado de Naturalidade</label>
                                <select name="naturalidade_estado_id" id="estadoNaturalidade"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500" onchange="carregarCidades(this.value, document.getElementById('cidadeNaturalidade'))">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estados as $estado) : ?>
                                    <option value="<?= $estado['id'] ?>" 
                                        <?= ($candidato['naturalidade_estado_id'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($estado['estado']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Cidade de Naturalidade -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Cidade de Naturalidade</label>
                                <select name="naturalidade_cidade_id" id="cidadeNaturalidade"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    <option value="">Primeiro selecione o estado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- ENDEREÇO -->
                    <div class="border-b pb-6">
                        <h2 class="text-xl font-bold mb-4 bg-gray-100 p-3 text-center">ENDEREÇO</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- CEP -->
                            <div>
                                <label class="block text-sm font-medium mb-2">CEP</label>
                                <input type="text" name="cep" id="cep"
                                    value="<?= htmlspecialchars($candidato['cep'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>

                            <!-- Estado do Endereço -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Estado</label>
                                <select name="estado_id" id="estadoEndereco"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500" onchange="carregarCidades(this.value, document.getElementById('cidadeEndereco'))">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($estados as $estado) : ?>
                                    <option value="<?= $estado['id'] ?>" 
                                        <?= ($candidato['estado_id'] ?? '') == $estado['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($estado['estado']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Município do Endereço -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Município</label>
                                <select name="municipio_id" id="cidadeEndereco"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                                    <option value="">Primeiro selecione o estado</option>
                                </select>
                                <input type="hidden" name="municipio" id="municipioNome" 
                                    value="<?= htmlspecialchars($candidato['municipio'] ?? '') ?>">
                            </div>

                            <!-- Endereço -->
                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium mb-2">Endereço</label>
                                <input type="text" name="endereco"
                                    value="<?= htmlspecialchars($candidato['endereco'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>

                            <!-- Número -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Número</label>
                                <input type="text" name="numero"
                                    value="<?= htmlspecialchars($candidato['numero'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>

                            <!-- Complemento -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Complemento</label>
                                <input type="text" name="complemento"
                                    value="<?= htmlspecialchars($candidato['complemento'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>

                            <!-- Bairro -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Bairro</label>
                                <input type="text" name="bairro"
                                    value="<?= htmlspecialchars($candidato['bairro'] ?? '') ?>"
                                    class="w-full p-2 border border-gray-300 rounded focus:border-green-500 focus:ring-1 focus:ring-green-500">
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-between items-center pt-6">
                        <a href="/painel" class="text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Voltar ao Painel
                        </a>
                        
                        <button type="submit" 
                            <?= !$pode_editar ? 'disabled' : '' ?>
                            class="<?= $pode_editar ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' ?> text-white font-bold py-3 px-6 rounded transition duration-200">
                            <i class="fas <?= $pode_editar ? 'fa-save' : 'fa-lock' ?> mr-2"></i><?= $pode_editar ? 'Salvar Dados' : 'Edição Bloqueada' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
<script>
// Script para desabilitar todos os campos quando não pode editar
document.addEventListener('DOMContentLoaded', function() {
    const podeEditar = <?= json_encode($pode_editar) ?>;
    
    if (!podeEditar) {
        // Desabilitar todos os inputs, selects e textareas do formulário
        const form = document.getElementById('formDados');
        if (form) {
            const campos = form.querySelectorAll('input:not([disabled]), select, textarea');
            campos.forEach(campo => {
                if (campo.name !== 'cpf') { // CPF já está desabilitado por padrão
                    campo.disabled = true;
                    campo.classList.add('bg-gray-100');
                }
            });
        }
        
        // Prevenir submissão do formulário
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                e.preventDefault();
                alert('Edição de dados não permitida durante período de inscrição ativo.');
            });
        }
    }
});

// === Função global: carregarCidades (usada nos onchange dos selects) ===
function carregarCidades(estadoId, cidadeSelect, selectedCidadeId = null) {
    if (!estadoId) {
        cidadeSelect.innerHTML = '<option value="">' + 'Primeiro selecione o estado' + '</option>';
        return;
    }
    cidadeSelect.innerHTML = '<option value="">' + 'Carregando...' + '</option>';
    const url = `/painel/buscar-cidades-por-estado/${encodeURIComponent(estadoId)}`;
    fetch(url)
      .then(response => {
          if (!response.ok) {
              return response.text().then(text => { throw new Error('Erro na requisição: ' + text); });
          }
          return response.json();
      })
      .then(data => {
          cidadeSelect.innerHTML = '<option value="">' + 'Selecione a cidade' + '</option>';
          if (Array.isArray(data)) {
              data.forEach(cidade => {
                  const option = document.createElement("option");
                  const id = cidade.id ?? cidade.codigo ?? cidade.codigo_ibge ?? cidade.municipio_id ?? cidade.cod_ibge;
                  const nome = cidade.cidade ?? cidade.nome ?? cidade.municipio ?? cidade.nome_municipio ?? cidade.descricao;
                  if (!id || !nome) return;
                  option.value = id;
                  option.textContent = nome;
                  if (selectedCidadeId && id == selectedCidadeId) option.selected = true;
                  cidadeSelect.appendChild(option);
              });
          } else {
              console.error("Erro ao carregar cidades: Dados inválidos recebidos. Payload =", data);
              cidadeSelect.innerHTML = '<option value="">' + 'Erro ao carregar cidades' + '</option>';
          }
      })
      .catch(error => {
          console.error("Erro ao carregar cidades:", error);
          cidadeSelect.innerHTML = '<option value="">' + 'Erro ao carregar cidades' + '</option>';
      });
}</script>
<script>document.addEventListener('DOMContentLoaded', function () {
        const candidatoData = {
            naturalidade_estado_id: <?= json_encode($candidato['naturalidade_estado_id'] ?? null) ?>,
            naturalidade_cidade_id: <?= json_encode($candidato['naturalidade_cidade_id'] ?? null) ?>,
            estado_id_endereco: <?= json_encode($candidato['estado_id'] ?? null) ?>,
            municipio_id_endereco: <?= json_encode($candidato['municipio_id'] ?? null) ?>
        };

        const estadoNaturalidadeSelect = document.getElementById('estadoNaturalidade');
        const cidadeNaturalidadeSelect = document.getElementById('cidadeNaturalidade');
        const estadoEnderecoSelect = document.getElementById('estadoEndereco');
        const cidadeEnderecoSelect = document.getElementById('cidadeEndereco');

        estadoNaturalidadeSelect.addEventListener('change', function () {
            carregarCidades(this.value, cidadeNaturalidadeSelect);
        });

        estadoEnderecoSelect.addEventListener('change', function () {
            carregarCidades(this.value, cidadeEnderecoSelect);
        });

        // Carregar cidades ao iniciar a página, se um estado já estiver selecionado
        if (candidatoData.naturalidade_estado_id) {
            carregarCidades(candidatoData.naturalidade_estado_id, cidadeNaturalidadeSelect, candidatoData.naturalidade_cidade_id);
        }

        if (candidatoData.estado_id_endereco) {
            carregarCidades(candidatoData.estado_id_endereco, cidadeEnderecoSelect, candidatoData.municipio_id_endereco);
        }
    });
    </script>
</body>
</html>

