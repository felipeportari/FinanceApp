<div align="center">

# 💰 FinanceApp

**Sistema de gerenciamento financeiro pessoal com inteligência artificial integrada**

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![OpenAI](https://img.shields.io/badge/OpenAI-GPT--4o-412991?style=flat-square&logo=openai&logoColor=white)](https://openai.com)

![Dashboard Preview](https://placehold.co/900x480/1E293B/94A3B8?text=Dashboard+Preview)

</div>

---

## ✨ Funcionalidades

- **Dashboard analítico** com KPIs do mês, gráfico de evolução e distribuição por categoria
- **Gestão de transações** — registro de gastos e lucros com filtros avançados
- **Categorias personalizadas** com cores e contadores de uso
- **Análise Inteligente** — diagnóstico financeiro gerado por IA via OpenAI
- **Tema claro/escuro** persistido por usuário
- **Dados demo** prontos para exploração imediata

---

## 🖥️ Preview

| Dashboard | Transações | Análise IA |
|:---------:|:----------:|:----------:|
| ![](https://placehold.co/280x180/F8FAFC/1E293B?text=Dashboard) | ![](https://placehold.co/280x180/F8FAFC/1E293B?text=Transações) | ![](https://placehold.co/280x180/F8FAFC/1E293B?text=IA) |

---

## 🗂️ Stack

| Camada | Tecnologia |
|--------|------------|
| Backend | Laravel 11 |
| Frontend | Blade + TailwindCSS (CDN) |
| Gráficos | Chart.js 4 |
| Interações | Alpine.js 3 |
| Banco de dados | MySQL 8 |
| IA | OpenAI GPT-4o-mini |

---

## ⚙️ Requisitos

Antes de começar, certifique-se de ter instalado:

- [PHP](https://php.net) **8.2 ou superior**
- [Composer](https://getcomposer.org) **2.x**
- [MySQL](https://mysql.com) **8.x** (ou MariaDB 10.6+)
- [Git](https://git-scm.com)

Para verificar suas versões:

```bash
php --version
composer --version
mysql --version
```

---

## 🚀 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/financeapp.git
cd financeapp
```

### 2. Instale as dependências PHP

```bash
composer install
```

### 3. Configure o arquivo de ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure o banco de dados

Crie o banco no MySQL:

```sql
CREATE DATABASE financeapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Edite o arquivo `.env` com suas credenciais:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=financeapp
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### 5. Execute as migrations e seeders

```bash
php artisan migrate --seed
```

Esse comando cria todas as tabelas e popula o banco com:
- 10 categorias padrão (Lazer, Esporte, Remédios, Transporte, etc.)
- 1 usuário demo com transações históricas dos últimos 2 meses

### 6. Inicie o servidor

```bash
php artisan serve
```

Acesse em: **[http://localhost:8000](http://localhost:8000)**

---

## 🔑 Credenciais de Acesso (Demo)

```
E-mail:  demo@financeapp.com
Senha:   password
```

---

## 🤖 Configurando a Análise com IA (OpenAI)

> Esta etapa é opcional. O sistema funciona normalmente sem ela.

**1.** Obtenha sua API Key em [platform.openai.com/api-keys](https://platform.openai.com/api-keys)

**2.** Adicione as seguintes variáveis ao seu `.env`:

```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_MODEL=gpt-4o-mini
```

**3.** Acesse **Análise Inteligente** no menu lateral e clique em **Gerar Análise Financeira**.

O sistema envia automaticamente um resumo estruturado dos seus dados para a IA, que retorna um diagnóstico com:

- Principais erros financeiros
- Categorias com desperdício
- Sugestões práticas de economia
- Oportunidades de aumento de lucro
- Diagnóstico geral

> **Custo estimado:** uma análise consome ~1.500 tokens. Com `gpt-4o-mini` o custo é inferior a **$0,01 por consulta**.

---

## 📁 Estrutura do Projeto

```
financeapp/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php       # Login / Logout
│   │   │   ├── DashboardController.php      # KPIs + dados dos gráficos
│   │   │   ├── TransactionController.php    # CRUD de transações
│   │   │   ├── CategoryController.php       # CRUD de categorias
│   │   │   ├── AiAnalysisController.php     # Integração OpenAI
│   │   │   └── ThemeController.php          # Toggle claro/escuro
│   │   └── Requests/
│   │       ├── StoreTransactionRequest.php  # Validação de transações
│   │       └── StoreCategoryRequest.php     # Validação de categorias
│   ├── Models/
│   │   ├── User.php                         # + relação transactions, helper isDarkTheme
│   │   ├── Category.php                     # + relação transactions
│   │   └── Transaction.php                  # + scopes, helpers de formatação
│   ├── Services/
│   │   ├── FinancialService.php             # Cálculos, dados dos charts, payload IA
│   │   └── OpenAiService.php               # HTTP client OpenAI + prompt estratégico
│   └── Providers/
│       └── AppServiceProvider.php           # Paginação Tailwind + locale pt_BR
│
├── database/
│   ├── migrations/                          # users, categories, transactions
│   ├── seeders/
│   │   ├── DatabaseSeeder.php               # Orquestrador + dados demo
│   │   └── CategorySeeder.php               # 10 categorias padrão
│   └── factories/
│       └── TransactionFactory.php           # Para testes
│
├── resources/views/
│   ├── layouts/app.blade.php                # Layout principal (sidebar + header)
│   ├── partials/
│   │   ├── sidebar.blade.php
│   │   └── header.blade.php
│   ├── auth/login.blade.php
│   ├── dashboard/index.blade.php            # Gráficos Chart.js
│   ├── transactions/{index,create,edit}
│   ├── categories/{index,create}
│   └── ai/analysis.blade.php               # Render markdown da IA
│
├── routes/web.php
├── config/services.php                      # Configuração OpenAI
└── .env.example
```

---

## 🗄️ Banco de Dados

```
users
 ├── id, name, email, password
 └── theme (light | dark)

categories
 ├── id, name
 ├── color (hex — usado nos gráficos)
 └── is_default (categorias padrão não podem ser removidas)

transactions
 ├── id, user_id, category_id
 ├── type (expense | income)
 ├── amount (decimal 12,2)
 ├── description, date
 └── índices em (user_id, date) e (user_id, type)
```

---

## 🧪 Testes

```bash
# Rodar todos os testes
php artisan test

# Com cobertura (requer Xdebug ou PCOV)
php artisan test --coverage
```

---

## 🛠️ Comandos Úteis

```bash
# Recriar banco com dados frescos
php artisan migrate:fresh --seed

# Limpar caches
php artisan optimize:clear

# Gerar link de storage (se necessário)
php artisan storage:link
```

---

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature: `git checkout -b feature/minha-feature`
3. Commit suas alterações: `git commit -m 'feat: adiciona minha feature'`
4. Push para a branch: `git push origin feature/minha-feature`
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👥 Feito em conjunto com

Este projeto foi desenvolvido em parceria com:

**[Claude Code](https://claude.ai/code)** — CLI de engenharia de software da Anthropic, responsável por arquitetura, código e toda a implementação técnica.

E com o apoio silencioso (mas atento) do nosso mascote favorito:

```
        __________
       /          \
      /  .-~~~~-.  \
     |  / .-~~-. \  |
     | | ( ͡° ͜ʖ ͡°) | |
     | |   \    /   | |
      \ \   `--'   / /
       `----------`
  ___   __________   ___
 /   \ /          \ /   \
|     V            V     |
|     |  carapaca  |     |
|     |     de     |     |
|     | sabedoria  |     |
|     |            |     |
 \___/ \__________/ \___/
         |      |
        _|      |_
       (_)      (_)
```

**Buddy Biscuit** — a tartaruga mais financeiramente consciente do mundo.
Ela não gasta com frescura. Só com alface e fichas de máquina de pelúcia.

> *"A lentidão não é preguiça. É planejamento financeiro de longo prazo."*
> — Buddy Biscuit

---

<div align="center">
  <sub>Construído com Laravel 11 + OpenAI · Em parceria com Claude Code & Buddy Biscuit 🐢</sub>
</div>
