# CM1 — Site Institucional CONINFOMS

Aplicação PHP (MVC) hospedada em `https://devoluto.com.br/cm1`.

## Como as URLs funcionam

O DocumentRoot do cPanel aponta para a pasta do projeto (`/cm1`), não para `Public/`.

- `.htaccess` na raiz reescreve as requisições para `Public/index.php`.
- `Core/Request` remove o prefixo `/cm1` **com ou sem barra final**, corrigindo o 404 de `devoluto.com.br/cm1`.
- Links da aplicação usam `url()` / `asset()` em vez de caminhos hardcoded.

## Requisitos

- PHP 8.1+
- MySQL 5.7+ / MariaDB (produção) ou SQLite (desenvolvimento)
- Extensões: pdo, pdo_mysql ou pdo_sqlite, mbstring, curl, gd

## Configuração

Copie `.env.example` para `.env` e ajuste:

```
APP_BASE_PATH=/cm1
DB_CONNECTION=mysql
DB_HOST=localhost
DB_NAME=coninfom_institucional
DB_USER=coninfom_admin
DB_PASS=********
```

Na primeira execução o sistema cria as tabelas (`users`, `articles`, `pages`, `settings`) e, se não houver usuários, cria o admin definido em `ADMIN_EMAIL` / `ADMIN_PASSWORD`.

## Painel administrativo

- URL: `/cm1/login`
- Credencial inicial (somente se a tabela `users` estiver vazia): ver `ADMIN_EMAIL` e `ADMIN_PASSWORD`.

## Desenvolvimento local

```bash
php -S 0.0.0.0:8080 -t Public Public/router.php
```

Acesse `http://localhost:8080/cm1` — o router do PHP built-in simula o prefixo de produção.

```bash
php tests/RequestPathTest.php
```

## Estrutura

```
Config/          App + Database
Controllers/     HTTP (rotas → ações)
Core/            Router, Auth, CSRF, Schema, Services
Models/          Acesso a dados (PDO prepared statements)
Views/           Templates
Public/          Front controller, uploads e assets
database/        schema.sql de referência
```
