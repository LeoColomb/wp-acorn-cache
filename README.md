# Acorn Cache for WordPress

> A cache manager powered by [Laravel](https://laravel.com/) through [Acorn](https://roots.io/acorn/).  

[![Build Status](https://github.com/LeoColomb/wp-acorn-cache/workflows/PHP%20CI/badge.svg)](https://github.com/LeoColomb/wp-acorn-cache/actions?query=workflow%3APHP%20CI)
[![Packagist](https://img.shields.io/packagist/v/LeoColomb/wp-acorn-cache.svg)](https://packagist.org/packages/LeoColomb/wp-acorn-cache)

## About

This plugin provides cache handlers for WordPress, using [Acorn](https://roots.io/acorn/) framework
based on [Laravel](https://laravel.com/) fondation.

* Enables the two cache wrappers for WordPress using [drop-ins](https://developer.wordpress.org/reference/functions/_get_dropins/).
  * Object Cache ([`object-cache.php`](dropins/object-cache.php))
  * Advanced Page Cache ([`advanced-cache.php`](dropins/advanced-cache.php))
* Compatible with any driver supported by Laravel (including Redis, Memcached and Array).
* Adds handy [WP-CLI](https://wp-cli.org/) commands.
* Targets modern software stacks.


## Usage

* Prepare your Composer file by adding custom paths ([more info](https://github.com/Koodimonni/Composer-Dropin-Installer#readme))
  ```json
  {
    "extra": {
      "dropin-paths": {
        "wordpress-root/wp-content/": [
          "package:leocolomb/wp-acorn-cache:dropins/object-cache.php",
          "package:leocolomb/wp-acorn-cache:dropins/advanced-cache.php"
        ]
      }
    }
  }
  ```

* Require the package in your Composer-managed WordPress instance
  ```bash
  composer require leocolomb/wp-acorn-cache
  ```

## Configuration

```bash
composer require leocolomb/wp-acorn-cache
```

### Driver

The cache driver must be setup as per [Laravel documentation](https://laravel.com/docs/cache#configuration).

```dotenv
CACHE_DRIVER=redis
```

### Handlers

## License

ISC © [Léo Colombaro](https://colombaro.fr)
