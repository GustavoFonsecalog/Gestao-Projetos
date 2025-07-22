# 🚀 Gestão de Projetos Internos

Sistema robusto para gestão de projetos internos, focado em colaboração, produtividade e rastreabilidade. Ideal para equipes que buscam controle, transparência e automação de processos.

---

## 📦 Tecnologias Utilizadas
- **Backend:** Laravel 12 (PHP 8+)
- **Frontend:** Vue.js 3 (Composition API)
- **SPA:** Inertia.js
- **CSS:** TailwindCSS 4
- **Banco:** SQLite (dev) / MySQL (prod)
- **PDF:** barryvdh/laravel-dompdf
- **Upload:** vue-filepond
- **Assinatura digital:** signature_pad
- **Testes:** PHPUnit

---

## ⚙️ Instalação e Execução

### 1. **Clone o repositório**
```bash
git clone https://github.com/seu-usuario/seu-repo.git
cd seu-repo/backend
```

### 2. **Configuração do Backend (Laravel)**

#### a) Instale as dependências PHP:
```bash
composer install
```

#### b) Instale as dependências JS:
```bash
npm install
```

#### c) Copie o arquivo de ambiente e configure:
```bash
cp .env.example .env
```
- Configure o banco (ex: `DB_CONNECTION=sqlite` e crie o arquivo `database/database.sqlite`)
- Configure o e-mail (Mailtrap recomendado para dev)

#### d) Gere a chave da aplicação:
```bash
php artisan key:generate
```

#### e) Rode as migrations e seeders:
```bash
php artisan migrate --seed
```

#### f) Inicie o servidor backend:
```bash
php artisan serve
```

### 3. **Configuração do Frontend (Vue.js)**

> O frontend está integrado via Inertia.js, mas para hot reload e assets:

```bash
npm run dev
```

Acesse: [http://localhost:8000](http://localhost:8000)

---

## 🧑‍💻 Ambiente de Desenvolvimento
- **PHP:** 8.1+
- **Node.js:** 18+
- **Composer:** 2+
- **NPM:** 9+
- **OS:** Windows, Linux ou Mac
- **Editor recomendado:** VSCode + extensão Volar (Vue 3)

### Variáveis importantes do `.env`:
- `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
- `APP_URL`, `APP_ENV`, `APP_DEBUG`

---

## 🛠️ Funcionalidades Principais
- CRUD completo de projetos, tarefas e subtarefas (Kanban)
- Feed de atividades e sugestões por projeto
- Upload de arquivos e assinatura digital
- Geração de PDF e envio de e-mail
- Controle de equipe, status, prioridade e orçamento
- Testes automatizados (PHPUnit)

---

## 🧪 Rodando os Testes

```bash
php artisan test
```

- Todos os testes de feature e unitários estão em `backend/tests/Feature/`
- Cobertura para projetos, tarefas, subtarefas, sugestões, feed e autenticação

---

## 💡 Dicas e Boas Práticas
- Sempre rode `php artisan migrate --seed` ao atualizar migrations
- Use `.env` diferente para produção
- Para debug, utilize `php artisan tinker` e `php artisan log:clear`
- Para rodar o frontend em produção: `npm run build`

---

> Projeto desenvolvido com foco em qualidade, escalabilidade e experiência do usuário. Pronto para ambientes exigentes e times que buscam excelência!
