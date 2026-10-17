# coninfoms

Site institucional em PHP (MVC simples, sem framework).

## Configuração central

- `Config/App.php`: nome da marca, domínio (`https://coninfoms.com.br`), detecção automática da pasta de hospedagem (`/cm1`, raiz etc.) e helpers de WhatsApp.
- `Core/helpers.php`: `url()`, `asset()`, `abs_url()`, `e()`, `brand_html()` e `whatsapp_link()`. Nunca use caminhos fixos como `/cm1/...` nas views/controllers.
- Se a detecção automática não servir ao ambiente, defina `APP_BASE_PATH` (ex.: `/cm1`) e `APP_PUBLIC_PATH` (ex.: `/cm1/Public`).

## Identidade visual

Paleta e tipografia em `Views/layout/brand-head.php` (config do Tailwind) e `Public/assets/css/brand.css`:
Dark Blue `#182C48`, Slate Gray `#323A42`, Light Gray `#F1F3F5`, Accent Blue `#2B5797`. Fonte Dev Sans (Light/Regular/Bold) com fallback para Inter.

## Contato

Canal único: WhatsApp (número e mensagem em Admin > Configurações). Página `/contato` e botão flutuante global no layout público.
