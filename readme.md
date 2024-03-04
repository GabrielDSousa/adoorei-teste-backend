# Projeto Adoorei API

Este projeto é uma API RESTful desenvolvida para o teste para desenvolvedor back-end na empresa Adoorei, em uma suposta loja chamada ABC que vende produtos de diferentes nichos. No momento, a API está focada na venda de celulares.

## Tecnologias Utilizadas

- Laravel 10
- Laravel Sail
- Laravel Jetstream com API Tokens
- MySQL
- Pest
- Postman

## Configuração do Ambiente de Desenvolvimento

### Pré-requisitos

- PHP 8.2
- PHP Extensions: XML, ZIP, Curl, DOM
- Composer
- Docker
- Docker Compose
- Postman

### Instalando o Sail em Aplicações Existente

Para usar o Sail em uma aplicação Laravel existente, você precisa instalá-lo usando o gerenciador de pacotes Composer. Claro, esses passos presumem que seu ambiente de desenvolvimento local existente permita que você instale as dependências do Composer:

```bash
composer require laravel/sail --dev
```

Faça uma cópia do arquivo .env.example e renomeie para .env.

```bash
cp .env.example .env
```

Após o Sail ter sido instalado, você pode executar o comando Artisan sail:install. Este comando irá publicar o arquivo docker-compose.yml do Sail na raiz de sua aplicação e modificar seu arquivo .env com as variáveis de ambiente necessárias para conectar-se aos serviços Docker:

```bash
php artisan sail:install
```
Para o funcionamento desejado, escolha os serviços **mysql** e **mailpit**.

Finalmente, você pode iniciar o Sail.

```bash
./vendor/bin/sail up
```

Gere a chave da aplicação.

```bash
./vendor/bin/sail artisan key:generate
```

Instale as dependências do projeto.

```bash
./vendor/bin/sail npm run install
```

Compile os assets.

```bash
./vendor/bin/sail npm run build
```

Prepare o banco de dados.

```bash
./vendor/bin/sail artisan migrate
```

```bash
./vendor/bin/sail artisan db:seed key:generate
```

Teste o sistema.

```bash
./vendor/bin/sail test
```

Acesse a aplicação em [localhost](http://localhost).

Para parar o Sail, execute o comando abaixo.

```bash
./vendor/bin/sail down
```

O sistema está configurado para enviar e-mails em ambiente de desenvolvimento. Para visualizar os e-mails enviados, acesse o Mailpit.

Para acessar o inbox do Mailpit, acesse [localhost:8025](http://localhost:8025).

Para gerar um token de acesso à API, acesse o frontend da aplicação e faça login. Em seguida, acesse a página de tokens de acesso.

Faça login com as credenciais de um usuário criado ou use o usuário de desenvolvimento local:

- E-mail: email@email.com
- Senha: password

## Funcionalidades

- Cadastrar usuário [Frontend]
- Autenticar usuário [Frontend]
- Gerar token de acesso da API [Frontend]
- Listar produtos disponíveis [API]
- Cadastrar nova venda [API]
- Consultar vendas realizadas [API]
- Consultar uma venda específica [API]
- Cancelar uma venda [API]
- Cadastrar novos produtos a uma venda [API]

## Documentação da API

A documentação completa da API está disponível em [documenter.getpostman.com](https://documenter.getpostman.com/view/4664269/2sA2xb7bQ3).

## Técnicas Utilizadas

- Autenticação com API Tokens
- Validação de Dados
- Tratamento de Exceções
- Tratamento de Erros
- Testes Automatizados
- TDD (Desenvolvimento Orientado a Testes)
- SOLID
- KISS
- Clean Code
- Conventional Commits
- Migration/Seeders