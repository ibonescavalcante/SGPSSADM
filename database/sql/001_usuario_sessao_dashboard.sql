-- Sessão única do painel /dashboard (PostgreSQL, schema pss)
-- Aplicar com: psql -f database/sql/001_usuario_sessao_dashboard.sql
-- ou cliente SQL apontando para o mesmo banco da aplicação.

CREATE TABLE IF NOT EXISTS pss.usuario_sessao_dashboard (
    usuario_id INTEGER NOT NULL PRIMARY KEY REFERENCES pss.usuario (id) ON DELETE CASCADE,
    token VARCHAR(64) NOT NULL,
    criado_em TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    ultimo_acesso TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
