# SabaIdea Challenge Project

## About

This is SabaIdea challenge

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
