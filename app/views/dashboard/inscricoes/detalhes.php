<?php $this->layout("dashboard/template") ?>

<!-- Conteúdo -->
<div class="col-lg-10 col-md-9 ms-sm-auto px-4 py-3">
    <?php
    // echo ("<pre>");
    // var_dump($detalhes);

    $pontuacao_total = 0;
    // $avaliador = 'Não avaliado';
    $extra_json = json_decode($detalhes[0]->extra_json, true);
    if (!empty($pontuacao)) {
        foreach ($pontuacao as $item) {
            $pontuacao_total += (float)$item->pontos;
        }
        // $avaliador = htmlspecialchars($pontuacao[0]->usuario_nome);
    }
    // if ($avaliador == 'Não avaliado') {
    //     $avaliador = $detalhes[0]->usuario_nome ?? 'Não avaliado';
    // }
    ?>
    <div class="card card-custom">
        <div class="card-body">
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger">
                    <?php
                    echo htmlspecialchars($_SESSION['erro']);
                    // apaga logo em seguida
                    unset($_SESSION['erro']);
                    ?>
                </div>
            <?php endif; ?>
            <!-- Dados do candidato -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Processo:</strong> <?php echo htmlspecialchars($detalhes[0]->pss_titulo); ?></p>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($detalhes[0]->candidato_nome); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($detalhes[0]->candidato_email); ?></p>
                    <!-- Email not found in var_dump -->
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($detalhes[0]->candidato_telefone); ?></p>
                    <!-- RG not found in var_dump -->
                    <p><strong>CPF:</strong> <?php echo htmlspecialchars($detalhes[0]->candidato_cpf); ?></p>
                    <p><strong>PCD:</strong> <?php $pcd = $extra_json['concorrer_pcd'];
                                                echo ($pcd); ?></p>

                    <?php

                    $laudo = $extra_json['laudo_pcd'] ?? null;
                    if (is_array($laudo)) {
                        foreach ($laudo as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars(str_replace("/var/www/", "/", $item)) . '" 
                                data-bs-toggle="modal" data-bs-target="#pdfModalTitulo">Ladudo pcd</a></p>';
                        }
                    } elseif (!empty($laudo)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars(str_replace("/var/www/", "/", $laudo)) . '" 
                                data-bs-toggle="modal" data-bs-target="#pdfModalTitulo">Ladudo pcd</a></p>';
                    }
                    ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Zona:</strong> <?php echo htmlspecialchars($detalhes[0]->cargo_zona); ?> </p>
                    <p><strong>Cargo:</strong> <?php echo htmlspecialchars($detalhes[0]->cargo_nome); ?> </p>
                    <p><strong>Data da Inscrição:</strong>
                        <?php echo date('d/m/Y', strtotime($detalhes[0]->inscricao_ini)); ?></p>
                    <p><strong>Inscrição:</strong> <?php echo htmlspecialchars($detalhes[0]->protocolo); ?></p>
                    <p><strong>Status:</strong> <span class="badge 
                                                            <?php
                                                            echo ($detalhes[0]->status == 'deferido') ? 'bg-success' : (($detalhes[0]->status == 'indeferido') ? 'bg-danger' : 'bg-warning');
                                                            ?>">
                            <?php echo htmlspecialchars(ucfirst($detalhes[0]->status)); ?></span>
                    </p>
                    <p><strong>Pontuação de titulos:</strong>
                        <?php echo number_format($pontuacao_total, 2, ',', '.'); ?></p>
                    <p><strong>Avaliador:</strong> <?php echo $avaliador; ?></p>
                </div>
            </div>

            <!-- Descrição -->
            <?php //die;
            ?>
            <!-- Anexos -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="mb-2"><i class="fas fa-clipboard-list me-2"></i>Requisitos</h5>
                    <?php
                    //
                    if (!empty($extra_json['documentos']['documento_identidade'])) : ?>
                        <p><a href="/requisitos<?php echo htmlspecialchars($extra_json['documentos']['documento_identidade']); ?>"
                                data-bs-toggle="modal" data-bs-target="#pdfModal">Documento de Identidade</a>
                            <?php
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($extra_json['documentos']['documento_identidade']); ?>')">Alterar</button>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>

                    <?php



                    $CUR_HABLITACAO_OPERACIONAL = $extra_json['documentos']['CUR_HABLITACAO_OPERACIONAL'] ?? null;

                    if (is_array($CUR_HABLITACAO_OPERACIONAL)) {
                        foreach ($CUR_HABLITACAO_OPERACIONAL as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">Curso de habilitação proficional</a>';

                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($CUR_HABLITACAO_OPERACIONAL)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($CUR_HABLITACAO_OPERACIONAL) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">Curso de habilitação proficional</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($CUR_HABLITACAO_OPERACIONAL); ?>')">Alterar</button>
                            <?php endif;
                        echo '</p>';
                    }


                    $CNH = null;
                    $possiveis_cnh = ['CNH_B_EAR', 'CNH_C_EAR', 'CNH_D_EAR', 'CNH_E_EAR', 'CNH_D_E_EAR', 'CNH_C_D_E_EAR'];
                    foreach ($possiveis_cnh as $campo) {
                        if (!empty($extra_json['documentos'][$campo])) {
                            $CNH = $extra_json['documentos'][$campo];
                            break; // achou o primeiro, sai do loop
                        }
                    }
                    if (is_array($CNH)) {
                        foreach ($CNH as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">CNH</a>';
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($CNH)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($CNH) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">CNH</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($CNH); ?>')">Alterar</button>
                            <?php endif;
                        echo '</p>';
                    }




                    $comprovante = $extra_json['documentos']['comprovante_escolaridade'] ?? null;

                    if (is_array($comprovante)) {
                        foreach ($comprovante as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">Comprovante de Escolaridade</a>';
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($comprovante)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($comprovante) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">Comprovante de Escolaridade</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($comprovante); ?>')">Alterar</button>
                            <?php endif;
                        echo '</p>';
                    }

                    $COMP_ESCOLARIDADE = $extra_json['documentos']['COMP_ESCOLARIDADE'] ?? null;

                    if (is_array($COMP_ESCOLARIDADE)) {
                        foreach ($COMP_ESCOLARIDADE as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">Comprovante de Escolaridade</a>';
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($COMP_ESCOLARIDADE)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($COMP_ESCOLARIDADE) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">Comprovante de Escolaridade</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($COMP_ESCOLARIDADE); ?>')">Alterar</button>
                            <?php endif;
                        echo '</p>';
                    }



                    $CURSO_CONDUTOR_EMERGENCIA = $extra_json['documentos']['CURSO_CONDUTOR_EMERGENCIA'] ?? null;

                    if (is_array($CURSO_CONDUTOR_EMERGENCIA)) {
                        foreach ($CURSO_CONDUTOR_EMERGENCIA as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">CURSO_CONDUTOR_EMERGENCIA</a>';
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($CURSO_CONDUTOR_EMERGENCIA)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($CURSO_CONDUTOR_EMERGENCIA) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">CURSO_CONDUTOR_EMERGENCIA</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($CURSO_CONDUTOR_EMERGENCIA); ?>')">Alterar</button>
                    <?php endif;
                        echo '</p>';
                    }

                    ?>
                    <?php
                    $CUR_SUPERIOR = $extra_json['documentos']['CUR_SUPERIOR'] ?? null;

                    if (is_array($CUR_SUPERIOR)) {
                        foreach ($CUR_SUPERIOR as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">CUR_SUPERIOR</a>';
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($CUR_SUPERIOR)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($CUR_SUPERIOR) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">CUR_SUPERIOR</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($CUR_SUPERIOR); ?>')">Alterar</button>
                    <?php endif;
                        echo '</p>';
                    }

                    ?>
                    <?php
                    $REGIS_CONS_CLASSE = $extra_json['documentos']['REGIS_CONS_CLASSE'] ?? null;

                    if (is_array($REGIS_CONS_CLASSE)) {
                        foreach ($REGIS_CONS_CLASSE as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">REGIS_CONS_CLASSE</a>';
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($REGIS_CONS_CLASSE)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($REGIS_CONS_CLASSE) . '" 
                            data-bs-toggle="modal" data-bs-target="#pdfModal">CURSO_CONDUTOR_EMERGENCIA</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($REGIS_CONS_CLASSE); ?>')">Alterar</button>
                    <?php endif;
                        echo '</p>';
                    }

                    ?>



                </div>

                <div class="col-md-6">
                    <h5 class="mb-2"><i class="fas fa-file-alt me-2"></i>Títulos </h5>
                    <?php if (!empty($extra_json['documentos']['comprovante_experiencia_declaracoes']) && is_array($extra_json['documentos']['comprovante_experiencia_declaracoes'])) : ?>
                        <?php foreach ($extra_json['documentos']['comprovante_experiencia_declaracoes'] as $file_path) : ?>
                            <p><a href="/titulos<?php echo htmlspecialchars($file_path); ?>" data-bs-toggle="modal"
                                    data-bs-target="#pdfModalTitulo" data-tipo-documento="Experiencia">Declaração de
                                    Experiência</a>
                                <?php if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                    <button class="btn btn-sm btn-outline-primary ms-2"
                                        onclick="alterarArquivo('<?php echo htmlspecialchars($file_path); ?>')">Alterar</button>
                                <?php endif; ?>
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php
                    $comprovante = $extra_json['documentos']['comprovante_escolaridade'] ?? null;
                    if (is_array($comprovante)) {
                        foreach ($comprovante as $item) {
                            echo '<p><a href="/requisitos' . htmlspecialchars($item) . '" 
                                data-bs-toggle="modal" data-bs-target="#pdfModalTitulo">Comprovante de Escolaridade</a>';
                            if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                <button class="btn btn-sm btn-outline-primary ms-2"
                                    onclick="alterarArquivo('<?php echo htmlspecialchars($item); ?>')">Alterar</button>
                            <?php endif;
                            echo '</p>';
                        }
                    } elseif (!empty($comprovante)) {
                        echo '<p><a href="/requisitos' . htmlspecialchars($comprovante) . '" 
                                data-bs-toggle="modal" data-bs-target="#pdfModalTitulo">Comprovante de Escolaridade</a>';
                        if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                            <button class="btn btn-sm btn-outline-primary ms-2"
                                onclick="alterarArquivo('<?php echo htmlspecialchars($comprovante); ?>')">Alterar</button>
                    <?php endif;
                        echo '</p>';
                    }
                    ?>

                    <?php if (!empty($extra_json['documentos']['certificados']) && is_array($extra_json['documentos']['certificados'])) : ?>
                        <?php foreach ($extra_json['documentos']['certificados'] as $file_path) : ?>
                            <p><a href="/titulos<?php echo htmlspecialchars($file_path); ?>" data-bs-toggle="modal"
                                    data-bs-target="#pdfModalTitulo" data-tipo-documento="Certificado">Certificados</a>
                                <?php if ($_SESSION['user']['nome'] == 'Administrador') : ?>
                                    <button class="btn btn-sm btn-outline-primary ms-2"
                                        onclick="alterarArquivo('<?php echo htmlspecialchars($file_path); ?>')">Alterar</button>
                                <?php endif; ?>
                            </p>
                        <?php endforeach; ?>
                    <?php endif; ?>


                </div>
            </div>
            <?php
            // echo ("<pre>");
            // var_dump($detalhes);
            // var_dump($pontuacao);
            // echo ("<pre>");
            // die;
            ?>
            <!-- Detalhes da Pontuação -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="mb-2">📋 Detalhes da Pontuação</h5>
                    <?php if (!empty($pontuacao)) : ?>
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Pontos</th>
                                    <th>Data da Avaliação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($pontuacao as $item) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item->item); ?></td>
                                        <td><?php echo number_format((float)$item->pontos, 2, ',', '.'); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($item->dt_avaliacao)); ?></td>
                                        <td>

                                            <form action="/dashboard/inscricoes/pontuacao/excluir" method="post"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta pontuação?');">
                                                <input type="hidden" name="pontuacao_id" value="<?php echo $item->id; ?>">
                                                <input type="hidden" name="inscricao_id"
                                                    value="<?php echo $detalhes[0]->id  ?>">
                                                <input type="hidden" name="avaliador_id"
                                                    value="<?php echo $item->avaliador_id; ?>">
                                                <button type="submit" class="btn  btn-sm">
                                                    <i class="fas fa-trash me-2" style="color:red;"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <p>Nenhuma pontuação registrada.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php // die; 
            ?>
            <!--div>
                <h5 class="mb-2"><i class="bi bi-clipboard-check me-2"></i>
                    Justificativa
                </h5>
            </div-->
            <!-- Form for justification and status update -->
            <form action="" method="post" class="p-3 bg-light rounded border">
                <div class="mb-3">
                    <label for="justificativa" class="form-label" style="font-weight: 700;">Justificativa</label>
                    <textarea name="justificativa" id="justificativa" rows="3" class="form-control"
                        placeholder="Escreva sua resposta aqui..."><?php echo !empty($detalhes[0]->motivo_cancelamento) ? htmlspecialchars($detalhes[0]->motivo_cancelamento) : ''; ?></textarea>
                </div>
                <input type="hidden" name="inscricao_id" value="<?php echo $detalhes[0]->id ?>" />
                <input type="hidden" name="avaliador_id" value="<?php echo $pontuacao[0]->avaliador_id ?? 0 ?>" />

                <div class="d-flex justify-content-between  gap-2">
                    <button type="button" class="btn btn-outline-secondary"
                        onclick="window.location.href='/dashboard/inscricoes'">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </button>

                    <div class="d-flex justify-content-end gap-2 ">
                        <select name="status" id="status" class="form-select w-auto">
                            <option value="DEFERIDO"
                                <?php echo ($detalhes[0]->status == 'deferido') ? 'selected' : ''; ?>>Deferida</option>
                            <option value="INDEFERIDO"
                                <?php echo ($detalhes[0]->status == 'indeferido') ? 'selected' : ''; ?>>Indeferida
                            </option>
                        </select>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
</div>
<!-- Modal PDF -->
<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel"><i class="bi bi-display me-1"></i>Documentos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdf-iframe-doc" src="" frameborder="0" style="width: 100%; height:700px;"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal PDF Título -->
<div class="modal fade" id="pdfModalTitulo" tabindex="-1" aria-labelledby="pdfModalTituloLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalTituloLabel"><i class="bi bi-display me-1"></i>Analisar Títulos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdf-iframe-titulo" src="" frameborder="0" style="width: 100%; height:700px;"></iframe>
            </div>
            <div class="modal-footer border-0">

                <form action="/dashboard/inscricoes/detalhes/pontuacao" method="post"
                    class="p-3 bg-light rounded border" style="width: 100%;">
                    <div class="mb-3">
                        <input type="text" id="doc_tipo" name="doc_tipo" class="form-control me-2 mb-1"
                            placeholder="Digite o tipo de documento">
                        <textarea name="justificativa_pontuacao" rows="3" class="form-control"
                            placeholder="Escreva sua justificativa aqui..." required></textarea>
                    </div>
                    <input type="hidden" name="inscricao_id" value="<?php echo $detalhes[0]->id ?>" />
                    <input type="hidden" name="avaliador_id" value="<?php echo $pontuacao[0]->avaliador_id ?? 0 ?>" />

                    <div class="d-flex justify-content-between   gap-2">
                        <div class="d-flex justify-content-between gap-2 border-2 " style="width: 100%;">
                            <input type="text" id="pontos-titulo" name="pontos-titulo" class="form-control me-2"
                                style="width: 200px;" placeholder="Digite a pontuação">
                            <div>
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Confirmar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal Documentos
        const pdfModal = document.getElementById('pdfModal');
        const iframeDoc = document.getElementById('pdf-iframe-doc');

        pdfModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const pdfUrl = button.getAttribute('href');
            iframeDoc.setAttribute('src', pdfUrl);
        });

        pdfModal.addEventListener('hidden.bs.modal', function() {
            iframeDoc.setAttribute('src', '');
        });

        // Modal Títulos
        const pdfModalTitulo = document.getElementById('pdfModalTitulo');
        const iframeTitulo = document.getElementById('pdf-iframe-titulo');

        pdfModalTitulo.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const pdfUrl = button.getAttribute('href');
            iframeTitulo.setAttribute('src', pdfUrl);
        });

        pdfModalTitulo.addEventListener('hidden.bs.modal', function() {
            iframeTitulo.setAttribute('src', '');
        });

        // Lógica de Avaliação de Títulos
        let tipoDocumentoAtual = '';

        pdfModalTitulo.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            tipoDocumentoAtual = button.getAttribute('data-tipo-documento');
        });

        const confirmarBtn = document.getElementById('confirmar-avaliacao-btn');
        confirmarBtn.addEventListener('click', function() {
            const pontosInput = document.getElementById('pontos-titulo');
            const pontos = pontosInput.value;
            const inscricaoId = <?php echo $detalhes[0]->id; ?>;

            if (pontos.trim() === '') {
                alert('Por favor, insira a pontuação.');
                return;
            }

            enviarAvaliacao(inscricaoId, tipoDocumentoAtual, pontos);
        });

        async function enviarAvaliacao(inscricao_id, tipo_documento, pontos) {
            const bodyContent = new FormData();
            bodyContent.append("inscricao_id", inscricao_id);
            bodyContent.append("tipo_documento", tipo_documento);
            bodyContent.append("pontos", pontos);

            try {
                const response = await fetch("/api/avaliacao-titulo", {
                    method: "POST",
                    body: bodyContent,
                });

                const data = await response.json();

                if (response.ok && data.sucesso) {
                    alert(data.mensagem);
                    const modal = bootstrap.Modal.getInstance(pdfModalTitulo);
                    modal.hide();
                    // Opcional: atualizar a pontuação na página dinamicamente
                    // location.reload(); 
                } else {
                    alert("Erro: " + (data.erro || "Não foi possível salvar a avaliação."));
                }
            } catch (error) {
                console.error("Erro ao enviar avaliação:", error);
                alert("Ocorreu um erro de comunicação com o servidor.");
            }
        }
    });
</script>

<!-- Modal Alterar Arquivo -->
<div class="modal fade" id="alterarArquivoModal" tabindex="-1" aria-labelledby="alterarArquivoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alterarArquivoModalLabel">Alterar Arquivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/dashboard/inscricoes/documento/alterar" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="inscricao_id" value="<?php echo $detalhes[0]->id; ?>">
                    <input type="hidden" id="documento-tipo" name="documento_tipo">
                    <div class="mb-3">
                        <label for="novo-arquivo" class="form-label">Selecione o novo arquivo</label>
                        <input class="form-control" type="file" id="novo-arquivo" name="novo_arquivo" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alterarArquivoModal = document.getElementById('alterarArquivoModal');
        alterarArquivoModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const documentoTipo = button.getAttribute('data-documento-tipo');
            const modalInput = alterarArquivoModal.querySelector('#documento-tipo');
            modalInput.value = documentoTipo;
        });
    });

    function alterarArquivo(tipoDocumento) {
        console.log("Alterando arquivo do tipo:", tipoDocumento);

        // Define o valor do campo no modal
        const campoTipo = document.getElementById('documento-tipo');
        if (campoTipo) campoTipo.value = tipoDocumento;

        // // Abre o modal via Bootstrap 5
        const modal = new bootstrap.Modal(document.getElementById('alterarArquivoModal'));
        modal.show();
    }
</script>