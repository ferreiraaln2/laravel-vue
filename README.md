# Pet Shop Manager

Um sistema simples de gerenciamento de pet shop desenvolvido com Laravel, Vue.js e Tailwind CSS.

## Funcionalidades

- Gerenciamento de pets (cadastro, edição, listagem e exclusão)
- Gerenciamento de serviços (cadastro, edição, listagem e exclusão)
- Agendamento de serviços (marcação, edição, listagem e cancelamento)
- Dashboard com visão geral dos dados

## Requisitos

- PHP 8.1 ou superior
- Composer
- Node.js e NPM
- MySQL ou outro banco de dados compatível

## Instalação

1. Clone o repositório:
```bash
git clone <url-do-repositorio>
cd pet-shop-manager
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Instale as dependências do JavaScript:
```bash
npm install
```

4. Copie o arquivo de ambiente e configure-o:
```bash
cp .env.example .env
```

5. Configure o arquivo `.env` com suas credenciais de banco de dados.

6. Gere a chave da aplicação:
```bash
php artisan key:generate
```

7. Execute as migrações para criar as tabelas no banco de dados:
```bash
php artisan migrate
```

8. (Opcional) Execute os seeders para popular o banco de dados com dados de exemplo:
```bash
php artisan db:seed
```

## Executando o projeto

1. Inicie o servidor de desenvolvimento do Laravel:
```bash
php artisan serve
```

2. Em outro terminal, compile os assets e inicie o servidor de desenvolvimento do Vite:
```bash
npm run dev
```

3. Acesse a aplicação em [http://localhost:8000](http://localhost:8000)

## Estrutura do Projeto

- `app/Models` - Modelos do Eloquent
- `app/Http/Controllers/Api` - Controladores da API
- `database/migrations` - Migrações do banco de dados
- `resources/js/components` - Componentes Vue.js
- `routes/api.php` - Rotas da API
- `routes/web.php` - Rotas web

## Tecnologias Utilizadas

- **Backend**: Laravel 10
- **Frontend**: Vue.js 3
- **CSS**: Tailwind CSS
- **Banco de Dados**: MySQL
- **Ferramentas de Build**: Vite

## Licença

Este projeto está licenciado sob a licença MIT.
