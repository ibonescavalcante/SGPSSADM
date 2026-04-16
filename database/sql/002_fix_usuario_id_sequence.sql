-- Corrige sequência de `pss.usuario.id` quando aparece erro duplicate key (pkey)
-- após inserts manuais, restore ou cópia de dados sem atualizar a sequence.
--
-- Executar no PostgreSQL (psql ou cliente SQL):

SELECT setval(
    pg_get_serial_sequence('pss.usuario', 'id')::regclass,
    COALESCE((SELECT MAX(id) FROM pss.usuario), 0),
    true
);
