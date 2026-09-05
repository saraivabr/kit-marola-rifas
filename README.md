<p align="center">
  <img src="public/images/logo.png" width="120" alt="Kit Marola Rifas" style="border-radius: 24px;" />
</p>

<h1 align="center">🌿 Kit Marola — Sistema de Rifas VIP & Tabacaria</h1>

<p align="center">
  <b>Plataforma completa de rifas digitais de alta conversão para tabacarias e headshops.</b><br>
  Desenvolvido com <b>Laravel 11</b>, <b>Vue 3 + Inertia.js</b>, <b>Filament 3</b>, <b>Woovi Pix Split</b> e o moderno <b>Astryx Design System (Meta Platforms)</b>.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue 3" />
  <img src="https://img.shields.io/badge/Inertia.js-v1-9553E9?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia" />
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind" />
  <img src="https://img.shields.io/badge/Design_System-Meta_Astryx-0064E0?style=for-the-badge&logo=meta&logoColor=white" alt="Astryx" />
  <img src="https://img.shields.io/badge/Gateway-Woovi_Pix_Split-00D586?style=for-the-badge&logo=pix&logoColor=white" alt="Woovi" />
  <img src="https://img.shields.io/badge/Admin-Filament_3-F59E0B?style=for-the-badge" alt="Filament" />
  <img src="https://img.shields.io/badge/PWA-Ready-22C55E?style=for-the-badge" alt="PWA" />
</p>

---

## 🌟 Destaques & Funcionalidades

### 💳 1. Gateway Woovi (OpenPix) com Split Automático 70/30
- **Split Pix em Tempo Real**: Rateio direto e instantâneo a cada pagamento aprovado via Webhook.
  - **70%** destinado ao parceiro/fornecedor via chave Pix configurada (`+5511988801548`).
  - **30%** retido automaticamente como taxa da plataforma.
- **Baixa Automatizada**: Confirmação instantânea do Pix com disparo de bilhetes e notificação.
- **Failover & Segurança**: Assinatura e idempotência de Webhook tratada pelo backend.

### 🎨 2. Astryx Design System (Meta Platforms)
- Interface de usuário refinada inspirada na especificação do **Astryx da Meta (Facebook)**:
  - **Design Tokens**: Superfícies escuras com elevação (`--astryx-color-background-surface`, `--astryx-color-background-card`), anéis de foco e inset rings.
  - **Escala de Raios Modular**: Baseada no grid de 4dp (`4px`, `8px`, `12px`, `16px`, `20px`, `28px`).
  - **Microinterações Suaves**: Curvas de easing Meta (`cubic-bezier(0.24, 1, 0.4, 1)`) e feedback tátil em botões e seletores.

### 📱 3. Mobile-First & PWA Experience
- **Navegação de App Nativo**: Barra inferior fixa (`PsrBottomBar.vue`) com consulta rápida de bilhetes via WhatsApp sem sair da tela.
- **PWA Instalável**: Suporte a tela cheia e ícone nativo no iOS e Android (`manifest.json` com `standalone`).
- **FOMO & Prova Social Ativa**:
  - Ticker flutuante de compras recentes em tempo real.
  - Contadores de compradores ao vivo por produto.
  - Alertas de escassez dinâmicos (*"🚨 Últimas Cotas"*, *"🔥 Alta Procura"*).

### 🛠️ 4. Painel Administrativo Filament 3 VIP
- **Tema Dark VIP Exclusivo**: Estilização premium com acentos dourados e pretos profundos.
- **Cards Verticais Touch-Friendly**: Otimização completa para gerenciamento direto pelo smartphone do lojista.
- **Gestão Completa**:
  - Criação e sorteio de rifas com múltiplos prêmios.
  - Ranking de maiores compradores em tempo real.
  - Histórico detalhado de pedidos, cotas geradas e comprovantes de Split Pix.
  - Gerenciamento de banners de slideshow e depoimentos de ganhadores.

---

## 🏗️ Arquitetura do Projeto

```text
psr-rifas-main/
├── app/
│   ├── Filament/                # Recursos do Painel Admin (Rifas, Pedidos, Usuários)
│   ├── Http/
│   │   ├── Controllers/         # Controllers da Home, Rifas e Checkout
│   │   └── Middleware/          # Proteções e validações de sessão
│   ├── Models/                  # Models Eloquent (Rifa, Order, Customer, etc.)
│   └── Services/Payment/        # Gateway Woovi (OpenPix) com lógica de Split Pix
├── resources/
│   ├── css/
│   │   ├── astryx.css           # Tokens oficiais do Astryx Design System (Meta)
│   │   └── app.css              # Reset e utilitários globais
│   ├── js/
│   │   ├── Components/          # Componentes Vue 3 (PsrButton, PsrCard, PsrBadge, etc.)
│   │   └── Pages/               # Telas Inertia.js (Home, Rifa, Pedido, Pagamento)
│   └── views/                   # Templates Blade base
├── public/
│   ├── manifest.json            # Configuração do PWA
│   └── images/                  # Imagens institucionais e logos
├── tailwind.config.js           # Mapeamento dos tokens Astryx para utilitários Tailwind
└── vite.config.js               # Bundler de alta performance
```

---

## 🚀 Como Rodar Localmente

### Pré-requisitos
- **PHP** >= 8.2 (com extensões `pdo_sqlite` ou `pdo_mysql`, `bcmath`, `curl`, `mbstring`)
- **Node.js** >= 18 e **pnpm**
- **Composer** >= 2.x

### 1. Clonar o repositório
```bash
git clone https://github.com/saraivabr/kit-marola-rifas.git
cd kit-marola-rifas
```

### 2. Instalar dependências
```bash
composer install
pnpm install
```

### 3. Configurar Variáveis de Ambiente
Copie o arquivo `.env.example` e gere a chave do app:
```bash
cp .env.example .env
php artisan key:generate
```

Configure as variáveis essenciais no `.env`:
```dotenv
APP_NAME="Kit Marola Rifas"
APP_URL=http://localhost:8000

# Gateway Woovi / OpenPix (Split Pix 70/30)
WOOVI_APP_ID=seu_app_id_woovi
WOOVI_PARTNER_PIX="+5511988801548"
WOOVI_SPLIT_PERCENTAGE=70
```

### 4. Executar Migrações e Seeds
```bash
php artisan migrate --seed
```

### 5. Iniciar Servidores de Desenvolvimento
Em terminais separados (ou usando concurrently):
```bash
# Servidor PHP
php artisan serve

# Servidor Vite com HMR
pnpm dev
```

Acesse no navegador: `http://localhost:8000`

---

## 🧪 Testes Automatizados

O projeto possui suíte completa de testes unitários e de componentes:

```bash
# Executar testes de frontend (Vitest)
npx vitest run

# Executar testes de backend (PHPUnit / Pest)
php artisan test
```

---

## 🚢 Deploy em Produção (Docker & VPS)

O projeto está otimizado para produção conteinerizada:

```bash
# Gerar bundle otimizado com tokens Astryx
pnpm build

# Limpar e aquecer caches do Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Exemplo de deploy com Docker:
```bash
docker run -d \
  --name kitmarola-prod \
  -p 127.0.0.1:8105:8000 \
  -v /opt/kitmarola:/app \
  -w /app \
  composer:latest php artisan serve --host=0.0.0.0 --port=8000
```

---

## 📄 Licença

Este projeto é desenvolvido e mantido por **Fellipe Saraiva**. Todos os direitos reservados.
