# Contexto do Projeto - EAD Control 2

## Stack
- Laravel 12 + stancl/tenancy (multi-tenant) + Sanctum + Inertia (React)
- MySQL: central `eadconto_gerente`, bancos tenant dinâmicos
- Legacy PHP (Ead.php, TemaEad.php, Qlib.php, Escola.php) convivendo com Laravel

## Última sessão (23/06/2026)

### O que foi criado

**Models:**
- `Curso` → `cursos`
- `Turma` → `turmas`
- `Matricula` → `matriculas` (já existia, foi melhorado)
- `Cliente` → `clientes`
- `Modulo` → `modulos_ead`
- `ConteudoEad` → `conteudo_ead`

**Resources:**
- `CursoResource` — nested modulos + atividades no payload
- `TurmaResource`, `MatriculaResource`, `ClienteResource`

**Controllers (api/):**
- `AuthController` — `login` (admin) + `loginCliente` (alunos)
- `CursoController` — CRUD com wrapper `{success, total, data}`
- `TurmaController`, `MatriculaController`, `ClienteController` — CRUD

**Routes (tenant.php):**
- `GET /api/v1/cursos` e `GET /api/v1/cursos/{id}` — **públicas**
- Demais rotas protegidas por `auth:sanctum`

**Seeders:**
- `AdminUserSeeder` — `fernando@maisaqui.com.br` / `123456`

### Payload dos cursos
No formato: `{success, total, data[{ID_antigo, slug, descricao_curso, modulos[{atividades[]}]}]}`
Relacionamento via JSON `conteudo`: `cursos.conteudo → modulos_ead.id`, `modulos_ead.conteudo → conteudo_ead.id`

### Database
- `DB_HOST=127.0.0.1 DB_PORT=3307 DB_DATABASE=eadconto_gerente DB_USERNAME=root DB_PASSWORD=root`
- Usuário admin: `fernando@maisaqui.com.br` / senha: `123456`

### Para rodar
```bash
php artisan db:seed --class=AdminUserSeeder
```

### Documentação completa
`docs/api.md`
