# Symfony Filesystem Ext

Lib for solving the most common problems with files.

![lib tests](https://github.com/yapro/symfony-filesystem-ext/actions/workflows/main.yml/badge.svg)

## Installation

Add as a requirement in your `composer.json` file or run for prod:
```sh
composer require yapro/symfony-filesystem-ext
```

As dev:
```sh
composer require yapro/symfony-filesystem-ext dev-main
```

## Development

Build:
```sh
docker build -t yapro/symfony-filesystem-ext:latest -f ./Dockerfile ./
```

Tests:
```sh
docker run --user=1000:1000 --rm -v $(pwd):/app -w /app yapro/symfony-filesystem-ext:latest bash -c "
  composer install --optimize-autoloader --no-scripts --no-interaction && 
  vendor/bin/simple-phpunit tests"
```

Installation dev`s requirements:
```sh
docker run --user=1000:1000 --add-host=host.docker.internal:host-gateway -it --rm -v $(pwd):/app -w /app yapro/symfony-filesystem-ext:latest bash
composer install -o
```
Debug PHP:
```sh
PHP_IDE_CONFIG="serverName=common" \
XDEBUG_SESSION=common \
XDEBUG_MODE=debug \
XDEBUG_CONFIG="max_nesting_level=200 client_port=9003 client_host=host.docker.internal" \
vendor/bin/simple-phpunit --cache-result-file=/tmp/phpunit.cache -v --stderr --stop-on-incomplete --stop-on-defect \
--stop-on-failure --stop-on-warning --fail-on-warning --stop-on-risky --fail-on-risky
```

Cs-Fixer:
```sh
wget https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v3.8.0/php-cs-fixer.phar && chmod +x ./php-cs-fixer.phar
docker run --user=1000:1000 --rm -v $(pwd):/app -w /app yapro/symfony-filesystem-ext:latest ./php-cs-fixer.phar fix --config=.php-cs-fixer.dist.php -v --using-cache=no --allow-risky=yes
```

Update phpmd rules:
```shell
wget https://github.com/phpmd/phpmd/releases/download/2.12.0/phpmd.phar && chmod +x ./phpmd.phar
docker run --user=1000:1000 --rm -v $(pwd):/app -w /app yapro/symfony-filesystem-ext:latest ./phpmd.phar . text phpmd.xml --exclude .github/workflows,vendor --strict --generate-baseline
```
