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

## Instalação nova na hospedagem

Guia completo: **[INSTALACAO.md](INSTALACAO.md)**

1. Envie os arquivos do pacote (ou clone o repositório) para `public_html/cm1`
2. Importe `database/install.sql` no MySQL (phpMyAdmin)
3. Copie `.env.example` → `.env` e preencha `DB_*`
4. Acesse `/cm1/login` com `admin@coninfoms.com.br` / `admin123` e troque a senha

Para gerar o ZIP de hospedagem localmente:

```bash
bash scripts/build-hosting-package.sh
```

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

Sem importar o SQL, na primeira execução o sistema ainda cria as tabelas via `Schema::ensure()` e, se não houver usuários, cria o admin de `ADMIN_EMAIL` / `ADMIN_PASSWORD`.

## Painel administrativo

- URL: `/cm1/login`
- Credencial do `install.sql`: `admin@coninfoms.com.br` / `admin123`

## Desenvolvimento local

```bash
php -S 0.0.0.0:8080 -t Public Public/router.php
```

Acesse `http://localhost:8080/cm1` — o router do PHP built-in simula o prefixo de produção.

```bash
php tests/RequestPathTest.php
```

## Deploy no cPanel (Git Version Control)

O erro **“The system could not contact the remote repository”** aparece no botão *Update from Remote*. **Não é bug do PHP**: o cPanel falhou ao fazer `git fetch` no GitHub.

Causas mais comuns, nesta ordem:

1. **Clone via SSH sem chave** (`git@github.com:CristiamMatos-1/cm1.git`) — o Git Version Control não pede senha e **não aceita chave com passphrase**.
2. **Chave SSH antiga do GitHub no servidor** (`known_hosts`) — o GitHub trocou a host key RSA; o cPanel recusa o contato.
3. **URL HTTPS com usuário/senha ou token** — o cPanel **proíbe** `https://user:token@github.com/...`.
4. **Firewall do provedor** bloqueando `github.com`.

O repositório é **público**. A correção mais simples é usar HTTPS **sem credenciais**.

### Passo a passo (recomendado)

1. No cPanel, abra **Terminal** (Avançado → Terminal) e teste:

```bash
git ls-remote https://github.com/CristiamMatos-1/cm1.git
```

- Se isso falhar (`Could not resolve host`, timeout, connection refused): o servidor **não alcança** o GitHub. Abra ticket no provedor pedindo liberação de `github.com` nas portas 443 e 22.
- Se listar refs (`HEAD`, `refs/heads/main`): a rede está ok. O problema é a URL/chave configurada no Git Version Control.

2. Em **Git Version Control → Manage**, confira:

- **Remote URL:** `https://github.com/CristiamMatos-1/cm1.git`  
  (não use `git@github.com:...` a menos que a chave SSH esteja configurada)
- **Checked-Out Branch:** `main`
- A URL **não** pode ter usuário, senha ou token.

3. Se a Remote URL estiver em SSH, ou o teste `git ls-remote` via SSH falhar, recrie o clone em HTTPS **ou** configure SSH sem senha:

```bash
# No Terminal do cPanel — chave SEM passphrase (Enter vazio nas perguntas)
ssh-keygen -t ed25519 -f ~/.ssh/cm1_github -C "cpanel-cm1" -N ""
chmod 600 ~/.ssh/cm1_github ~/.ssh/config 2>/dev/null || true
cat ~/.ssh/cm1_github.pub
```

Cole a chave pública em GitHub → repositório **cm1** → **Settings → Deploy keys → Add deploy key**.

Crie/edite `~/.ssh/config`:

```
Host github.com
    HostName github.com
    User git
    IdentityFile ~/.ssh/cm1_github
    IdentitiesOnly yes
```

Se o GitHub recusar a host key antiga:

```bash
ssh-keygen -R github.com
ssh-keyscan -t ed25519 github.com >> ~/.ssh/known_hosts
ssh -T git@github.com
```

4. Volte em **Pull or Deploy** → **Update from Remote**. Só depois clique em **Deploy HEAD Commit**.

### Deploy HEAD Commit

O cPanel **só faz deploy** se existir `.cpanel.yml` na raiz (já versionado neste projeto). Confira o `DEPLOYPATH` nesse arquivo (`/home/coninfom/public_html/cm1`) e ajuste o usuário da conta se for diferente.

Se o **Repository Path** do Git Version Control **já for** `public_html/cm1`, o *Update from Remote* já atualiza o site. Mesmo assim o `.cpanel.yml` é necessário para o botão *Deploy HEAD Commit* não falhar.

Se o working tree local no cPanel tiver arquivos alterados (uploads, `.env`, `sessions/`), o deploy pode recusar “dirty working tree”. Não commite esses arquivos; eles já estão no `.gitignore`.

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
