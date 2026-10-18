# Instalação do CM1 (CONINFOMS) na hospedagem

Pacote PHP MVC para cPanel / Apache com DocumentRoot apontando para a pasta do site (ex.: `public_html/cm1`).

## Requisitos

- PHP **8.1+** (recomendado 8.2/8.3)
- Extensões: `pdo`, `pdo_mysql`, `mbstring`, `curl`, `gd`, `fileinfo`
- MySQL 5.7+ ou MariaDB 10.3+
- `mod_rewrite` habilitado

## 1. Enviar os arquivos

1. Extraia o ZIP na pasta do site, por exemplo:
   - `public_html/cm1/` (URL: `https://seudominio.com.br/cm1`)
2. A estrutura deve ficar assim:

```text
cm1/
  .env.example
  .htaccess
  index.php
  INSTALACAO.md
  README.md
  Config/
  Controllers/
  Core/
  Models/
  Views/
  Public/
  database/
    install.sql
    schema.sql
  storage/
  sessions/
```

## 2. Criar e importar o banco

1. No cPanel → **MySQL® Databases**, crie:
   - um banco (ex.: `coninfom_institucional`)
   - um usuário com senha forte
   - associe o usuário ao banco com **ALL PRIVILEGES**
2. Abra o **phpMyAdmin**, selecione o banco e importe:
   - `database/install.sql`

Isso cria **todas as tabelas** e os dados iniciais (admin, páginas, configurações e 1 artigo de exemplo).

### Credencial inicial do painel

| Campo  | Valor                     |
|--------|---------------------------|
| URL    | `/cm1/login`              |
| E-mail | `admin@coninfoms.com.br`  |
| Senha  | `admin123`                |

**Altere a senha no primeiro acesso** (Usuários → Editar).

## 3. Configurar o `.env`

Na raiz do site:

```bash
cp .env.example .env
```

Edite `.env` (ou pelo Gerenciador de Arquivos):

```env
APP_ENV=production
APP_DEBUG=0
APP_BASE_PATH=/cm1

DB_CONNECTION=mysql
DB_HOST=localhost
DB_NAME=coninfom_institucional
DB_USER=seu_usuario_mysql
DB_PASS=sua_senha_mysql
DB_CHARSET=utf8mb4

ADMIN_EMAIL=admin@coninfoms.com.br
ADMIN_PASSWORD=admin123
```

- Se o site estiver na **raiz do domínio** (não em `/cm1`), use `APP_BASE_PATH=` (vazio) e ajuste o `RewriteBase` no `.htaccess` para `/`.
- `ADMIN_*` só é usado se a tabela `users` estiver vazia (o `install.sql` já cria o admin).

## 4. Permissões de escrita

Garanta que o PHP possa gravar em:

```text
sessions/
storage/
storage/logs/
storage/downloads/
Public/uploads/
Public/uploads/articles/
Public/uploads/banners/
Public/uploads/videos/
```

No cPanel, permissão típica de pastas: `755` (ou `775` se o usuário do PHP for o mesmo do FTP).

## 5. Verificar o site

1. Home: `https://seudominio.com.br/cm1`
2. Login: `https://seudominio.com.br/cm1/login`
3. Sitemap: `https://seudominio.com.br/cm1/sitemap.xml`
4. Robots: `https://seudominio.com.br/cm1/robots.txt`

## 6. Pós-instalação recomendada

No painel (`/cm1/admin`):

1. **Configurações** — nome, logo, WhatsApp, redes sociais, hero
2. **Usuários** — trocar senha do admin
3. **Banners / Matérias / Páginas / Downloads / Avisos** — conteúdo real
4. Revisar a **Política de Privacidade**

## Segurança

- Não publique o arquivo `.env`
- Pastas `Config`, `Core`, `Controllers`, `Models`, `Views`, `sessions`, `storage`, `database` já são bloqueadas pelo `.htaccess`
- Mantenha `APP_DEBUG=0` em produção

## Atualização futura (Git no cPanel)

Se usar **Git Version Control**, o arquivo `.cpanel.yml` copia o código para o caminho de deploy. Após atualizar o código, **não reimporte** `install.sql` (isso apaga dados). O sistema também aplica migrações leves via `Schema::ensure()` na primeira requisição.

## Problemas comuns

| Sintoma | Solução |
|---------|---------|
| 404 em `/cm1` | Confirme `.htaccess` na raiz e `RewriteBase /cm1/` |
| Erro de banco | Confira `DB_*` no `.env` e se o usuário tem permissão |
| Upload falha | Permissão de escrita em `Public/uploads` e `storage/downloads` |
| Sessão não grava | Pasta `sessions/` gravável |
