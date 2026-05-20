# Changelog

## 2026-05-19

- Ajustes de deploy (Render + Supabase): uso de `DB_URL` (pooler) para evitar problemas com IPv6-only no host `db.<ref>.supabase.co`.
- Docker/Apache: configuracao de `PORT` via variavel de ambiente no start do container.

