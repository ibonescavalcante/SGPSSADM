-- Tipos de documento alinhados às chaves em pss.inscricao.extra_json->'documentos'
-- e laudo_pcd na raiz do JSON. Executar manualmente (sem ON CONFLICT: exige UNIQUE em codigo).

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'documento_identidade', 'requisito', 'Documento de Identidade', NULL, TRUE, FALSE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'documento_identidade');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'CUR_HABLITACAO_OPERACIONAL', 'requisito', 'Curso de habilitação profissional', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'CUR_HABLITACAO_OPERACIONAL');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT v.codigo, 'requisito', v.nome, NULL, TRUE, TRUE
FROM (VALUES
  ('CNH_B_EAR', 'CNH categoria B'),
  ('CNH_C_EAR', 'CNH categoria C'),
  ('CNH_D_EAR', 'CNH categoria D'),
  ('CNH_E_EAR', 'CNH categoria E'),
  ('CNH_D_E_EAR', 'CNH categorias D e E'),
  ('CNH_C_D_E_EAR', 'CNH categorias C, D e E')
) AS v(codigo, nome)
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = v.codigo);

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'comprovante_escolaridade', 'requisito', 'Comprovante de Escolaridade', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'comprovante_escolaridade');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'COMP_ESCOLARIDADE', 'requisito', 'Comprovante de Escolaridade (COMP_ESCOLARIDADE)', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'COMP_ESCOLARIDADE');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'CURSO_CONDUTOR_EMERGENCIA', 'requisito', 'Curso condutor emergência', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'CURSO_CONDUTOR_EMERGENCIA');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'CUR_SUPERIOR', 'requisito', 'Curso superior', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'CUR_SUPERIOR');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'REGIS_CONS_CLASSE', 'requisito', 'Registro conselho de classe', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'REGIS_CONS_CLASSE');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'laudo_pcd', 'requisito', 'Laudo PCD', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'laudo_pcd');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'comprovante_experiencia_declaracoes', 'titulo', 'Declaração de Experiência', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'comprovante_experiencia_declaracoes');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'certificados', 'titulo', 'Certificados', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'certificados');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'POS_GRADUACAO_LATU_SENSU', 'titulo', 'Pós-Graduação Lato Sensu', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'POS_GRADUACAO_LATU_SENSU');

INSERT INTO pss.tipos_documento (codigo, tipo, nome, descricao, ativo, multiplos_arquivos)
SELECT 'CUR_EXTENSAO', 'titulo', 'Curso de Extensão', NULL, TRUE, TRUE
WHERE NOT EXISTS (SELECT 1 FROM pss.tipos_documento td WHERE td.codigo = 'CUR_EXTENSAO');
