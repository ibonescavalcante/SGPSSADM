<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Inscrição - PSS Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .print-page {
                page-break-inside: avoid;
                margin: 0;
                padding: 20px;
            }
            .print-header {
                margin-bottom: 30px;
            }
            .print-section {
                margin-bottom: 20px;
                page-break-inside: avoid;
            }
            .print-logo {
                max-height: 60px !important;
                width: auto !important;
                display: block !important;
            }
            .print-title {
                font-size: 24px !important;
                font-weight: bold !important;
                margin-bottom: 10px !important;
            }
            .print-subtitle {
                font-size: 18px !important;
                margin-bottom: 20px !important;
            }
            .print-protocol {
                font-size: 20px !important;
                font-weight: bold !important;
                background-color: #fef3c7 !important;
                border-left: 4px solid #f59e0b !important;
                padding: 15px !important;
                margin-bottom: 25px !important;
            }
            .print-data-section {
                border: 2px solid #059669 !important;
                border-radius: 8px !important;
                padding: 20px !important;
                margin-bottom: 20px !important;
            }
            .print-data-title {
                font-size: 16px !important;
                font-weight: bold !important;
                color: #059669 !important;
                margin-bottom: 15px !important;
            }
            .print-grid {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 10px !important;
            }
            .print-grid-item {
                margin-bottom: 8px !important;
            }
            .print-footer {
                text-align: center !important;
                font-size: 12px !important;
                color: #6b7280 !important;
                border-top: 1px solid #d1d5db !important;
                padding-top: 15px !important;
                margin-top: 30px !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-green-600 text-white p-4 no-print">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h1 class="text-xl font-bold">PSS Platform</h1>
            </div>
            <div class="flex space-x-4">
                <button onclick="baixarPDF()" 
                   class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded">📄 Baixar PDF</button>
                <button onclick="window.print()" class="bg-gray-500 hover:bg-gray-700 px-4 py-2 rounded">🖨️ Imprimir</button>
            </div>
        </div>
    </header>

    <div class="container mx-auto p-6 print-page" id="comprovante-content">
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-4xl mx-auto">
            <!-- Cabeçalho do Comprovante -->
            <div class="text-center mb-8 print-header">
                <div class="flex items-center justify-center mb-4">
                    <img src="/assets/img/logo_prefeitura.svg" alt="Logo Prefeitura" class="h-13 mr-4 print-logo">
                    <div class="text-green-600 font-bold text-2xl print-title">PREFEITURA MUNICIPAL DE PARAUAPEBAS</div>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2 print-title">COMPROVANTE DE INSCRIÇÃO</h1>
                <h2 class="text-lg text-gray-600 print-subtitle">Processo Seletivo Simplificado</h2>
            </div>

            <!-- Protocolo em destaque -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 text-center mb-6 print-protocol">
                <p class="text-lg font-bold text-yellow-800">
                    Protocolo: <?= htmlspecialchars($inscricao["protocolo"]) ?>
                </p>
            </div>

            <!-- Informações do PSS -->
            <div class="border-2 border-green-600 rounded-lg p-6 mb-6 print-data-section print-section">
                <h3 class="text-lg font-bold text-green-600 mb-4 print-data-title">📋 DADOS DO PROCESSO SELETIVO</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print-grid">
                    <div class="print-grid-item">
                        <strong>Processo:</strong> <?= htmlspecialchars($pss["titulo"]) ?>
                    </div>
                    <?php if (!empty($pss["numero_edital"])): ?>
                    <div class="print-grid-item">
                        <strong>Edital:</strong> <?= htmlspecialchars($pss["numero_edital"]) ?>
                    </div>
                    <?php endif; ?>
                    <div class="print-grid-item">
                        <strong>Cargo:</strong> <?= htmlspecialchars($cargo["nome"]) ?>
                    </div>
                    <div class="print-grid-item">
                        <strong>Zona:</strong> <?= ucfirst($cargo["zona"]) ?>
                    </div>
                    <?php if (!empty($cargo["microrregiao"])): ?>
                    <div class="print-grid-item">
                        <strong>Microrregião:</strong> <?= ucfirst($cargo["microrregiao"]) ?>
                    </div>
                    <?php endif; ?>
                    <div class="print-grid-item">
                        <strong>Total de Vagas:</strong> <?= $cargo["vagas_total"] ?>
                    </div>
                    <?php if ($cargo["vagas_pcd"] > 0): ?>
                    <div class="print-grid-item">
                        <strong>Vagas PCD:</strong> <?= $cargo["vagas_pcd"] ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($cargo["salario"])): ?>
                    <div class="print-grid-item">
                        <strong>Salário:</strong> R$ <?= number_format($cargo["salario"], 2, ",", ".") ?>
                    </div>
                    <?php endif; ?>
                    <div class="print-grid-item">
                        <strong>Data da Inscrição:</strong> 
                        <?php 
                        // Configurar timezone para Brasília
                        date_default_timezone_set("America/Sao_Paulo");
                        echo date("d/m/Y H:i:s", strtotime($inscricao["dt_inscricao"] . " UTC"));
                        ?>
                    </div>
                </div>
            </div>

            <!-- Informações do Candidato -->
            <div class="border-2 border-blue-600 rounded-lg p-6 mb-6 print-data-section print-section">
                <h3 class="text-lg font-bold text-blue-600 mb-4 print-data-title">👤 DADOS DO CANDIDATO</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print-grid">
                    <div class="print-grid-item">
                        <strong>Nome:</strong> <?= htmlspecialchars($candidato["nome"]) ?>
                    </div>
                    <?php if (!empty($candidato["nome_social"])): ?>
                    <div class="print-grid-item">
                        <strong>Nome Social:</strong> <?= htmlspecialchars($candidato["nome_social"]) ?>
                    </div>
                    <?php endif; ?>
                    <div class="print-grid-item">
                        <strong>CPF:</strong> <?= htmlspecialchars($candidato["cpf"]) ?>
                    </div>
                    <div class="print-grid-item">
                        <strong>Email:</strong> <?= htmlspecialchars($candidato["email"]) ?>
                    </div>
                    <?php if (!empty($candidato["celular"])): ?>
                    <div class="print-grid-item">
                        <strong>Telefone:</strong> <?= htmlspecialchars($candidato["celular"]) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($candidato["data_nascimento"])): ?>
                    <div class="print-grid-item">
                        <strong>Data de Nascimento:</strong> <?= date("d/m/Y", strtotime($candidato["data_nascimento"])) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($candidato["documento_tipo"]) && !empty($candidato["documento_numero"])): ?>
                    <div class="print-grid-item">
                        <strong>Documento:</strong> <?= htmlspecialchars($candidato["documento_tipo"]) ?>: <?= htmlspecialchars($candidato["documento_numero"]) ?>
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
                    <div class="col-span-2 print-grid-item">
                        <strong>Endereço:</strong> <?= htmlspecialchars(implode("", $endereco_completo)) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dados da Inscrição -->
            <div class="border-2 border-purple-600 rounded-lg p-6 mb-6 print-data-section print-section">
                <h3 class="text-lg font-bold text-purple-600 mb-4 print-data-title">📝 DADOS DA INSCRIÇÃO</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 print-grid">
                    <div class="print-grid-item">
                        <strong>Data/Hora da Inscrição:</strong> 
                        <?php 
                        // Configurar timezone para Brasília
                        date_default_timezone_set("America/Sao_Paulo");
                        echo date("d/m/Y H:i:s", strtotime($inscricao["dt_inscricao"] . " UTC"));
                        ?>
                    </div>
                    <div class="print-grid-item">
                        <strong>Status:</strong> 
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            <?= $inscricao["status"] === "pendente" ? "bg-yellow-100 text-yellow-800" : 
                                ($inscricao["status"] === "apta" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800") ?>">
                            <?= ucfirst($inscricao["status"]) ?>
                        </span>
                    </div>
                    <div class="print-grid-item">
                        <strong>Protocolo:</strong> <?= htmlspecialchars($inscricao["protocolo"]) ?>
                    </div>
                    <?php 
                    $extra_data = json_decode($inscricao["extra_json"], true);
                    if (!empty($extra_data["concorrer_pcd"]) && $extra_data["concorrer_pcd"] === "sim"): 
                    ?>
                    <div class="print-grid-item">
                        <strong>Concorre como PCD:</strong> Sim
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informações Importantes -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 print-section">
                <h3 class="text-lg font-bold text-blue-800 mb-2">📌 INFORMAÇÕES IMPORTANTES</h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• Este comprovante é válido como prova de inscrição no processo seletivo.</li>
                    <li>• Guarde este documento para futuras consultas.</li>
                    <li>• Acompanhe o cronograma do processo através do site oficial da Prefeitura.</li>
                    <li>• Para acompanhar o andamento, acesse sua área do candidato com seu CPF e senha.</li>
                    <li>• Em caso de dúvidas, entre em contato através dos canais oficiais da Prefeitura.</li>
                </ul>
            </div>

            <!-- Rodapé -->
            <div class="text-center text-sm text-gray-500 border-t pt-4 print-footer">
                <p>Este comprovante é válido como prova de inscrição no processo seletivo.</p>
                <p>Guarde este documento para futuras consultas.</p>
                <p>Gerado em: 
                    <?php 
                    // Usar a data/hora da inscrição do banco, não a atual
                    date_default_timezone_set("America/Sao_Paulo");
                    echo date("d/m/Y H:i:s", strtotime($inscricao["dt_inscricao"] . " UTC"));
                    ?>
                </p>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="text-center mt-6 no-print">
            <button onclick="baixarPDF()" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg mr-4">
                📄 Baixar PDF
            </button>
            <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg mr-4">
                🖨️ Imprimir Comprovante
            </button>
            <a href="/painel" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg">
                🏠 Voltar ao Painel
            </a>
        </div>
    </div>

    <script>
        function baixarPDF() {
            const element = document.getElementById("comprovante-content");
            const opt = {
                margin: [0.5, 0.5, 0.5, 0.5], // Margens: top, left, bottom, right em polegadas
                filename: "comprovante_inscricao_<?= $inscricao["protocolo"] ?>.pdf",
                image: { type: "jpeg", quality: 0.98 },
                html2canvas: { scale: 4 }, // Aumentar a escala para melhor qualidade
                jsPDF: { unit: "in", format: "a4", orientation: "portrait" } // Usar formato A4
            };
            
            html2pdf().set(opt).from(element).save();
        }

        // Auto-print se solicitado via URL
        if (window.location.search.includes("print=1")) {
            window.onload = function() {
                setTimeout(function() {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>

