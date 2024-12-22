# Marvel Project

Este projeto utiliza Laravel, Vue 3, Quasar e Vite. Siga as instruções abaixo para configurar o ambiente de desenvolvimento.

## Requisitos

- PHP ^8.4
- Node.js 23.5.0
- Composer 2.8.4
- laravel 11.31

## Dependências Principais
 
- Quasar: ^2.17.5
- Vue 3: ^3.2.47
- Vite: ^6.0
- Sass: ^1.83.0

## Configuração do Ambiente

### Passo 1: Clonar o Repositório

Clone o repositório para sua máquina local:

```sh
git clone <URL-do-repositorio>
```

### Passo 2: Instalar Dependências do PHP

Use o Composer para instalar as dependências do PHP:

```sh
composer install
```

### Passo 3: Instalar Dependências do Node.js

Use o npm para instalar as dependências do Node.js:

```sh
npm install
```

### Passo 4: Configurar o Ambiente

Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente conforme necessário:

```sh
cp .env.example .env
```

### Passo 5: Gerar a Chave da Aplicação

Gere a chave da aplicação Laravel:

```sh
php artisan key:generate
```

### Passo 6: Migrar o Banco de Dados

Execute as migrações para configurar o banco de dados:

```sh
php artisan migrate
```

### Passo 7: Iniciar o Servidor de Desenvolvimento

Use o comando abaixo para iniciar o servidor de desenvolvimento:

```sh
npm run dev
```

### Passo 8: Acessar a Aplicação

Abra o navegador e acesse `http://localhost:3000` para ver a aplicação em execução.