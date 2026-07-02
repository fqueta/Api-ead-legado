# API EAD Control - Documentação

## Base URL
```
http://{tenant_domain}/api/v1
```

---

## Autenticação

### Login (admin)
```
POST /api/v1/login
Content-Type: application/json

{
    "email": "admin@exemplo.com",
    "password": "123456"
}
```

**Resposta (200):**
```json
{
    "message": "Authorized",
    "status": 200,
    "data": {
        "token": "1|sanctum_token_aqui"
    }
}
```

### Login do Aluno (clientes)
```
POST /api/v1/login-cliente
Content-Type: application/json

{
    "email": "aluno@exemplo.com",
    "senha": "senha_do_aluno"
}
```

**Resposta (200):**
```json
{
    "message": "Authorized",
    "status": 200,
    "data": {
        "token": "2|sanctum_token_aqui",
        "cliente": {
            "id": 1,
            "nome": "João",
            "sobrenome": "Silva",
            "email": "aluno@exemplo.com"
        }
    }
}
```

> **Nota**: Admin usa `password` (bcrypt na tabela `users`). Aluno usa `senha` (texto plano na tabela `clientes`).

Todas as demais rotas exigem:
```
Authorization: Bearer {token}
```

---

## Cursos

**Rota pública** (não precisa de token).

### Listar cursos
```
GET /api/v1/cursos
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `per_page` | int | Itens por página (padrão 15) |
| `page` | int | Número da página |
| `ativo` | string | Filtrar por ativo (`s`/`n`) |
| `categoria` | string | Filtrar por categoria |
| `destaque` | string | Filtrar destaque (`s`/`n`) |
| `search` | string | Busca em nome, título e descrição |

**Resposta:**
```json
{
    "success": true,
    "total": 4,
    "data": [
        {
            "ID_antigo": "12785",
            "ativo": "s",
            "titulo": "O uso do Livox no Contexto Escolar",
            "nome": "O uso do Livox no Contexto Escolar",
            "slug": "o-uso-do-livox-no-contexto-escolar",
            "descricao_curso": "<div class=\"card bg-light\">...",
            "duracao": "16",
            "unidade_duracao": "hrs",
            "valor": "180",
            "inscricao": "180",
            "parcelas": "1",
            "valor_parcela": "180",
            "tipo": "2",
            "publicar": "s",
            "instrutor": "6",
            "observacoes": "",
            "perguntas": [],
            "modulos": [
                {
                    "active": "s",
                    "module_id": "13",
                    "name": "Módulo I",
                    "title": "Módulo I",
                    "description": "",
                    "duration": "8280",
                    "type_duration": "seg",
                    "atividades": [
                        {
                            "active": "s",
                            "name": "Introdução",
                            "title": "Introdução",
                            "type_activities": "video",
                            "content": "https://player.vimeo.com/video/426410622",
                            "id_antigo": "12837",
                            "description": "<p>...</p>",
                            "duration": "840",
                            "type_duration": "seg"
                        }
                    ]
                }
            ],
            "config": {
                "adc": { "cor": "FFFFFF" },
                "gratis": "n",
                "cover": {
                    "title": "...",
                    "url": "https://..."
                }
            }
        }
    ]
}
```

### Obter curso
```
GET /api/v1/cursos/{id}
```
`{id}` pode ser ID numérico ou `token`.

**Resposta:** mesmo formato do item dentro do `data[]` acima, dentro de `{"success":true,"total":1,"data":[...]}`.

### Criar curso
```
POST /api/v1/cursos
```
Protegido. Campos: `nome`*, `titulo`*, `url`, `categoria`, `tipo`, `descricao`, `descricao_site`, `meta_descricao`, `meta_titulo`, `valor`, `inscricao`, `parcelas`, `valor_parcela`, `duracao`, `unidade_duracao`, `ativo`, `destaque`, `professor`, `ordenar`, `config` (object), `conteudo` (array).

### Atualizar curso
```
PUT /api/v1/cursos/{id}
```

### Excluir curso
```
DELETE /api/v1/cursos/{id}
```
Soft delete (`excluido = 's'`).

---

## Turmas

**Rotas protegidas.**

```
GET/POST    /api/v1/turmas
GET/PUT/DEL /api/v1/turmas/{id}
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `per_page` | int | Itens por página |
| `id_curso` | int | Filtrar por curso |
| `ativo` | string | `s`/`n` |

Inclui relacionamento `curso`.

### Exportar turmas (CSV)
```
GET /api/v1/turmas/export
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `id_curso` | int | Filtrar por curso |
| `ativo` | string | `s`/`n` |
| `data_inicio` | date | Filtrar por data início (>=) |
| `data_fim` | date | Filtrar por data início (<=) |

**Resposta:** CSV com BOM, colunas: ID, Curso, Turma, Início, Fim, Data Início, Máx. Alunos, Matriculados, Ativo.

---

## Matrículas

**Rotas protegidas.**

```
GET/POST    /api/v1/matriculas
GET/PUT/DEL /api/v1/matriculas/{id}
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `per_page` | int | Itens por página |
| `id_cliente` | int | Filtrar por cliente |
| `id_curso` | int | Filtrar por curso |
| `id_turma` | int | Filtrar por turma |
| `status` | string | Filtrar por status |
| `ativo` | string | `s`/`n` |

Inclui relacionamentos `cliente`, `curso`, `turma`.

### Obter matrícula
```
GET /api/v1/matriculas/{id}
```
`{id}` pode ser ID numérico ou `token`.

**Resposta:**
```json
{
    "ativo": "s",
    "desconto": "0.00",
    "id": "1",
    "id_cliente": "019ee1b0-6bd0-706d-bb09-2454201e71e8",
    "id_consultor": "019eb0af-8aac-7215-9fec-3419ce303cc6",
    "id_curso": "2",
    "id_responsavel": "019eb0af-8aac-7215-9fec-3419ce303cc6",
    "id_turma": "0",
    "inscricao": "0.00",
    "meta": {
        "gera_valor": "",
        "parcelada": false,
        "parcelas": "12",
        "texto_desconto": "",
        "validade": "14"
    },
    "obs": "",
    "orc": [],
    "situacao_id": "17",
    "subtotal": "249.00",
    "total": "249.00"
}
```

### Listar matrículas
```
GET /api/v1/matriculas
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `per_page` | int | Itens por página (padrão 15) |
| `id_cliente` | int | Filtrar por cliente |
| `id_curso` | int | Filtrar por curso |
| `id_turma` | int | Filtrar por turma |
| `status` | string | Filtrar por status |
| `ativo` | string | `s`/`n` |

**Resposta:** array paginado com formato `{success: true, total: X, data: [...]}` onde cada item no `data` segue o mesmo formato do endpoint `GET /api/v1/matriculas/{id}` acima.

Inclui relacionamentos `cliente`, `curso`, `turma`.

### Exportar matrículas (JSON)
```
GET /api/v1/matriculas/export
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `id_cliente` | int | Filtrar por cliente |
| `id_curso` | int | Filtrar por curso |
| `id_turma` | int | Filtrar por turma |
| `status` | string | Filtrar por status |
| `ativo` | string | `s`/`n` |

**Resposta:**
```json
{
    "success": true,
    "total": 699,
    "data": [
        {
            "ativo": "s",
            "desconto": "0.00",
            "id": "1",
            "id_cliente": "019ee1b0-6bd0-706d-bb09-2454201e71e8",
            "id_consultor": "019eb0af-8aac-7215-9fec-3419ce303cc6",
            "id_curso": "2",
            "id_responsavel": "019eb0af-8aac-7215-9fec-3419ce303cc6",
            "id_turma": "0",
            "inscricao": "0.00",
            "meta": {
                "gera_valor": "",
                "parcelada": false,
                "parcelas": "12",
                "texto_desconto": "",
                "validade": "14"
            },
            "obs": "",
            "orc": [],
            "situacao_id": "17",
            "subtotal": "249.00",
            "total": "249.00"
        }
    ]
}
```

---

## Clientes

**Rotas protegidas.**

```
GET/POST    /api/v1/clientes
GET/PUT/DEL /api/v1/clientes/{id}
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `per_page` | int | Itens por página |
| `ativo` | string | `s`/`n` |
| `search` | string | Busca em nome, sobrenome, email, CPF |

Busca por ID, `token`, CPF ou email.

### Exportar clientes (CSV)
```
GET /api/v1/clientes/export
```

| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| `ativo` | string | `s`/`n` |
| `search` | string | Busca em nome, sobrenome, email, CPF |

**Resposta:** CSV com BOM, colunas: ID, Nome, Sobrenome, E-mail, CPF, Celular, Telefone, Endereço, Número, Bairro, Cidade, UF, CEP, Data Nascimento, Estado Civil, Profissão, Ativo, Criado em.

---

## Endpoints Adicionais

### Presença em massa
```
POST /api/v1/add-presenca-massa
```

---

## Modelo de Dados

### Diagrama Relacional

```
cursos (tab10)
  ├── id (PK)
  ├── nome, titulo, url (slug), categoria, tipo
  ├── descricao, descricao_site, meta_descricao, meta_titulo, obs
  ├── valor, inscricao, parcelas, valor_parcela
  ├── duracao, unidade_duracao
  ├── ativo, destaque, token
  ├── conteudo (JSON → [{idItem: modulos_ead.id}])
  ├── config (JSON)
  ├── professor (FK), autor, ordenar
  └── data, atualizado, excluido, deletado
       │
       ├── modulos_ead (tab38)
       │     ├── id (PK), token
       │     ├── nome, nome_exibicao, descricao, url
       │     ├── ativo, professor, ordenar, autor, token_curso
       │     ├── conteudo (JSON → [{idItem: conteudo_ead.id}])
       │     ├── config (JSON)
       │     └── data, atualizado, excluido, deletado
       │          │
       │          └── conteudo_ead (tab39)
       │                ├── id (PK), token
       │                ├── nome, nome_exibicao, tipo (video/prova/...)
       │                ├── descricao, duracao, unidade_duracao
       │                ├── video, tipo_link_video, url, gratis, ativo
       │                ├── id_curso, token_modulo, token_curso, token_prova
       │                ├── start, end, config (JSON), ordenar, autor
       │                └── data, atualizado, excluido, deletado
       │
       ├── turmas (tab11)
       │     ├── id (PK), id_curso (FK)
       │     ├── nome, inicio, fim, data_inicio
       │     ├── max_alunos, ativo
       │     └── data, atualizado, excluido, deletado
       │
       └── matriculas (tab12)
             ├── id (PK), id_cliente (FK), id_curso (FK), id_turma (FK)
             ├── token, status, validade, data_inicio
             ├── contrato, pagamento_asaas, ativo, numero_aluno
             ├── config (JSON), tipo_curso, orc (JSON)
             └── data, atualizado, excluido, deletado

clientes (tab15)
  ├── id (PK), Nome, sobrenome, Email, email, Cpf, cpf
  ├── token, Celular, Telefone, Tel, telefonezap
  ├── Endereco, Numero, Bairro, Cidade, Uf, Cep, Compl
  ├── Ident, DtNasc2, estado_civil, profissao
  ├── id_asaas, config (JSON), senha, canac
  ├── nacionalidade, permissao, origem, ativo
  └── data, atualizado, excluido, deletado
       │
       └── matriculas (tab12) — id_cliente (FK)
```

### Relacionamento Curso → Módulos → Atividades

A hierarquia é armazenada via JSON nas colunas `conteudo`:

1. `cursos.conteudo` = `[{"idItem": 13}, {"idItem": 42}]` → IDs em `modulos_ead`
2. `modulos_ead.conteudo` = `[{"idItem": 12837}, {"idItem": 12838}]` → IDs em `conteudo_ead`
3. A ordem dos itens no JSON define a ordenação

---

## Estrutura de Diretórios

```
app/
├── Models/
│   ├── Curso.php            # cursos
│   ├── Turma.php            # turmas
│   ├── Matricula.php        # matriculas
│   ├── Cliente.php          # clientes
│   ├── Modulo.php           # modulos_ead
│   ├── ConteudoEad.php      # conteudo_ead
│   ├── Tenant.php           # stancl/tenancy
│   └── User.php             # users (tenant)
│
├── Http/
│   ├── Controllers/
│   │   └── api/
│   │       ├── AuthController.php        # login, login-cliente
│   │       ├── CursoController.php       # CRUD cursos
│   │       ├── TurmaController.php       # CRUD turmas
│   │       ├── MatriculaController.php   # CRUD matriculas
│   │       └── ClienteController.php     # CRUD clientes
│   │
│   └── Resources/
│       ├── CursoResource.php     # Payload c/ modulos + atividades aninhados
│       ├── TurmaResource.php
│       ├── MatriculaResource.php
│       └── ClienteResource.php
│
├── Services/
│   ├── Escola.php        # Lógica legado EAD
│   └── Qlib.php          # Helpers legado
│
├── Helpers/
│   ├── Ead.php           # CRUD forms legado
│   ├── TemaEad.php       # Front-end legado
│   ├── helpers.php
│   └── StringHelper.php
│
database/
├── migrations/
│   └── tenant/            # Migrations do banco tenant
└── seeders/
    └── AdminUserSeeder.php   # php artisan db:seed --class=AdminUserSeeder

routes/
└── tenant.php             # Rotas da API (multi-tenant)
```

---

## Seeder

```bash
php artisan db:seed --class=AdminUserSeeder
```

Cria usuário admin: `fernando@maisaqui.com.br` / `123456`

---

## Convenções do Sistema Legado

| Convenção | Detalhe |
|-----------|---------|
| **Soft delete** | `excluido` (`s`/`n`) + `deletado` (`s`/`n`) + `reg_excluido`/`reg_deletado` |
| **Timestamps** | `data` (created_at) e `atualizado` (updated_at) |
| **Token público** | Coluna `token` (gerado via `uniqid()`) |
| **Config dinâmica** | Coluna `config` em JSON em todas as tabelas principais |
| **Estrutura de módulos** | Coluna `conteudo` em JSON: `[{"idItem": <id>}, ...]` |

---

## Mapeamento Payload × DB (Cursos)

| Campo Payload | Coluna DB | Model |
|---------------|-----------|-------|
| `ID_antigo` | `cursos.id` | Curso |
| `slug` | `cursos.url` | Curso |
| `descricao_curso` | `cursos.descricao` | Curso |
| `publicar` | `cursos.ativo` | Curso |
| `instrutor` | `cursos.professor` | Curso |
| `observacoes` | `cursos.obs` | Curso |
| `unidade_duracao` | `cursos.unidade_duracao` (mapping: `h` → `hrs`) | Curso |
| `modulos[].module_id` | `modulos_ead.id` | Modulo |
| `modulos[].name` | `modulos_ead.nome_exibicao` ou `nome` | Modulo |
| `modulos[].atividades[].id_antigo` | `conteudo_ead.id` | ConteudoEad |
| `modulos[].atividades[].type_activities` | `conteudo_ead.tipo` | ConteudoEad |
| `modulos[].atividades[].content` | `conteudo_ead.video` + `tipo_link_video` | ConteudoEad (monta URL) |
