# LaraDocker

Initialize docker-compose environment for a Laravel Project.


## Prerequizits

- docker
- docker-compose
- git
- composer

## Usage

1. Install a new Laravel project: `laravel new projectname123`
2. Run `cd projectname123`
3. Run `composer require alphaws/laradocker --dev`
4. Run `php artisan laradocker:install`

## Services

- nginx
- php:8-fpm
- mariadb
- composer
- node
- redis
