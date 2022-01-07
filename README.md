# URL Shortener

## About

A framework with pure php language.

Changes a big URL into tiny URL. (with Custom Dashboard)

## Requirements

- Docker (version 19.03.0 or later)
- Docker compose

## Deploy

- Copy `.env.example` to `.env`
- Add ROOT PASSWORD and PASSWORD to the .env file
- Add a new secret key to the .env file
- Run these commands:

```shell
docker-compose up -d
docker-compose exec web composer install
docker-compose exec web php src/Database/migration.php
```

- Api address:
  http://localhost:8100

Developed By `Erfan Saheni`
