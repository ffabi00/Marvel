# Marvel Project

Este projeto utiliza Laravel, Vue 3, Quasar e Vite. Siga as instruções abaixo para configurar o ambiente de desenvolvimento.

## Link para acessar em produção
- http://marvel.lab3d.com.br

## Requisitos

- PHP 8.4
- Node.js 23.5.0
- Composer 2.8.4
- laravel 11.31

## Dependências Principais

- Quasar: 2.17.5
- Vue 3: 3.2.47
- Vite: 6.0
- Sass: 1.83.0
- Redis: 4.7.0

##  Chaves de acesso
- Precisa gerar chaves de acesso na url para alimentar a aplicação e adicionar nas variaveis no .env
- https://developer.marvel.com/documentation/getting_started

# Variaveis para carregar
- API_MARVEL_KEY=
- API_MARVEL_SECRET=

## Levantar ambiente Redis
- Para melhorar a performace das consultas

## Configuração do Ambiente

### Passo 1: Clonar o Repositório

Clone o repositório para sua máquina local:

```sh
git clone https://github.com/ffabi00/Marvel.git
```

### Passo 2: Instalar Dependências do PHP

Use o Composer para instalar as dependências do PHP:

```sh
composer install && composer update
```

### Passo 3: Instalar Dependências do Node.js

Use o npm para instalar as dependências do Node.js:

```sh
npm install
```

### Passo 4: Instalar Quasar Extras e Quasar CI

Instale as dependências adicionais do Quasar:

```sh
npm install @quasar/extras @quasar/ci
```

### Passo 5: Atualizar Quasar

Atualize o Quasar para garantir que você tenha a versão mais recente:

```sh
quasar upgrade
```

### Passo 6: Configurar o Ambiente

Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente conforme necessário:

```sh
cp .env.example .env
```

### Passo 7: Gerar a Chave da Aplicação

Gere a chave da aplicação Laravel:

```sh
php artisan key:generate
```

### Passo 8: Migrar o Banco de Dados

Execute as migrações para configurar o banco de dados:

```sh
php artisan migrate
```

### Passo 9: Iniciar o Servidor de Desenvolvimento

Se estiver configurando para um ambiente de desenvolvimento, você precisará iniciar dois servidores em terminais diferentes ou em segundo plano:

```sh
npm run dev
php artisan serve
```

Caso contrário, apenas o comando abaixo é suficiente:

```sh
php artisan serve
```

### Passo 10: Acessar a Aplicação

Abra o navegador e acesse `http://localhost:3000` para ver a aplicação em execução.