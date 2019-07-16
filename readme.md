# Slim 4 app

- Core: [Slim 4 (beta)](//github.com/slimphp/Slim)
- HTTP: [Slim-PSR7](//github.com/slimphp/Slim-Psr7)
- Container: [PHP-DI](//github.com/PHP-DI/PHP-DI)
- DB: [Cycle](//github.com/cycle) (ORM and Migrations) [[docs]](//github.com/cycle/docs)
- Rendering: [Twig](//github.com/twigphp/Twig) / [Plates](//github.com/thephpleague/plates) / [pug-php](//github.com/pug-php/pug)
- Front: [UIkit](//github.com/uikit/uikit)
- Debug: [Kint](//github.com/kint-php/kint)

# Install

PHP 7.3 required

```bash
composer create-project --prefer-dist roxblnfk/slim4-basic-app my-app
composer update
```

# Configure

Configure your database in `/config/database.yaml` [[Doc]](//github.com/cycle/docs/blob/master/basic/connect.md)

## Migrations

Migrations config file: `/config/migrations.yaml`

- Generate migration file from diff between Entities and DB structures:
    ```bash
    bin/run migrate:generate
    ```
- Run migrations:
    ```bash
    bin/run migrate:up
    ```

# TODO

- Routes
- Tests
- CS
