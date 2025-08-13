<p align="center">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<h1 align="center">API Locadora de Carros</h1>

<p align="center">
    <a href="https://laravel.com/docs/10.x">Laravel 10</a> •
    <a href="https://jwt.io/">JWT Auth</a>
</p>

## Sobre o Projeto

Este projeto é uma API desenvolvida em Laravel 10 como parte de um curso prático. O objetivo é criar uma API RESTful para uma locadora de carros, implementando autenticação JWT para segurança das rotas.

### Funcionalidades

- Cadastro, listagem, atualização e remoção
- Gerenciamento de clientes, carros(marca e modelo) e locação
- Autenticação e autorização via JWT
- Rotas protegidas para operações sensíveis

## Tecnologias Utilizadas

- [Laravel 10](https://laravel.com/docs/10.x)
- [JWT Auth](https://jwt-auth.readthedocs.io/en/develop/)
- PHP 8+
- MySQL ou outro banco de dados relacional

## Como Executar

1. Clone o repositório:
     ```bash
     git clone <url-do-repositorio>
     cd app_locadora_carros
     ```
2. Instale as dependências:
     ```bash
     composer install
     ```
3. Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente.
4. Gere a chave da aplicação:
     ```bash
     php artisan key:generate
     ```
5. Execute as migrations:
     ```bash
     php artisan migrate
     ```
6. Instale o JWT Auth e gere a chave:
     ```bash
     php artisan jwt:secret
     ```
7. Inicie o servidor:
     ```bash
     php artisan serve
     ```

## Autenticação

A autenticação é feita via JWT. Para acessar rotas protegidas, obtenha um token autenticando-se com e-mail e senha e envie o token no header `Authorization` das requisições.

## Contribuição

Sinta-se à vontade para abrir issues ou pull requests para melhorias.

## Licença

Este projeto está sob a licença [MIT](https://opensource.org/licenses/MIT).
