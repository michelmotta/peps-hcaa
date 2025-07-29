
# PEPS - Programa de Educação Permanente em Saúde

![Logo do Laravel](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

[![Última Versão Estável](https://img.shields.io/packagist/v/laravel/framework)](https://packagist.org/packages/laravel/framework)
[![Status dos Testes](https://github.com/michelmotta/peps-hcaa/actions/workflows/run-laravel-tests.yml/badge.svg)](https://github.com/michelmotta/peps-hcaa/actions/workflows/laravel-tests.yml)
[![Licença](https://img.shields.io/packagist/l/laravel/framework)](https://packagist.org/packages/laravel/framework)

## Sobre o Projeto

O PEPS é uma plataforma robusta de e-learning construída com o framework Laravel. Ele oferece uma solução completa para criar, gerenciar e distribuir conteúdo educacional através de aulas, tópicos, vídeos e questionários interativos.

Este software foi desenvolvido como parte do programa de Mestrado Profissional da Faculdade de Computação (FACOM) da Universidade Federal de Mato Grosso do Sul (UFMS), em parceria com o Hospital de Câncer Alfredo Abrão (HCAA).

## Principais Funcionalidades

- **Gerenciamento de Usuários e Perfis**: Cadastro e autenticação seguros, com perfis de usuário detalhados, incluindo biografias e fotos.
- **Estrutura de Cursos e Aulas**: Um sistema hierárquico para organizar o conteúdo em aulas e tópicos.
- **Manipulação Avançada de Vídeos**:
  - Upload de vídeos de forma assíncrona com barra de progresso em tempo real.
  - Geração automática de miniaturas (thumbnails) usando FFMpeg.
  - Pré-visualização de vídeos em lightbox (Fancybox).
  - Opção para alterar ou excluir vídeos pré-carregados antes de salvar o formulário.
- **Questionários Interativos**: Um construtor dinâmico de questionários que permite aos instrutores criar perguntas de múltipla escolha para qualquer tópico.
- **Anexos de Arquivos**: Uploader de arquivos com funcionalidade de arrastar e soltar (drag-and-drop) para adicionar materiais complementares aos tópicos.
- **Painel Administrativo**: Uma poderosa interface de backend para gerenciar usuários, aulas, tópicos e o conteúdo do site.
- **Frontend Moderno**: Construído com Bootstrap e módulos JavaScript para uma experiência de usuário responsiva e interativa, com diálogos de confirmação personalizados usando SweetAlert2.

## Instalação e Configuração (com Docker)

Siga estes passos para executar o projeto em sua máquina local usando Docker.

### Pré-requisitos

- Docker
- Docker Compose

### 1. Clonar o Repositório

```bash
git clone https://github.com/michelmotta/peps-hcaa.git
cd peps-hcaa
```

### 2. Configuração do Ambiente

Copie o arquivo de ambiente de exemplo:

```bash
cp .env.example .env
```

Abra o arquivo `.env` e certifique-se de que as variáveis do banco de dados correspondem às do seu `docker-compose.yml`:

```
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

### 3. Iniciar os Containers

```bash
docker compose up -d --build
```

### 4. Instalar Dependências

```bash
# Instalar dependências do PHP
docker compose exec php-fpm composer install

# Instalar dependências do JavaScript
docker compose exec php-fpm npm install
```

### 5. Configuração da Aplicação

```bash
docker compose exec php-fpm php artisan key:generate
docker compose exec php-fpm php artisan migrate
docker compose exec php-fpm php artisan storage:link
docker compose exec php-fpm npm run build
```

### 6. Acessando a Aplicação

Acesse no navegador:

```
http://localhost:8080
```

## Licença

Este projeto é um software de código aberto licenciado sob a licença MIT.
