# Acorn Cache for WordPress

> A cache manager powered by [Laravel](https://laravel.com/) through [Acorn](https://roots.io/acorn/).  

[![Build Status](https://github.com/LeoColomb/wp-acorn-cache/workflows/PHP%20CI/badge.svg)](https://github.com/LeoColomb/wp-acorn-cache/actions?query=workflow%3APHP%20CI)
[![Packagist](https://img.shields.io/packagist/v/LeoColomb/wp-acorn-cache.svg)](https://packagist.org/packages/LeoColomb/wp-acorn-cache)

> [!Warning]  
> 🚧 This project is still rather experimental.  
> Most probably not functional out of the box and definitely not ready for production.

## About

This plugin provides cache handlers for WordPress, using [Acorn](https://roots.io/acorn/) framework
based on [Laravel](https://laravel.com/) fondation.

* Enables the two cache wrappers for WordPress using [drop-ins](https://developer.wordpress.org/reference/functions/_get_dropins/).
  * Object Cache ([`object-cache.php`](dropins/object-cache.php))
  * Advanced Page Cache (optional) ([`advanced-cache.php`](dropins/advanced-cache.php))
* Compatible with any driver supported by Laravel (including Redis, Memcached and Array).
* Adds handy [WP-CLI](https://wp-cli.org/) commands.
* Targets modern software stacks.


## Usage

* Prepare your Composer file by adding custom paths.  
  Replace `<wordpress-root/wp-content/>` with your WordPress content path,
  `web/app/` with [Bedrock](https://roots.io/bedrock/).  
  See [more info](https://github.com/Koodimonni/Composer-Dropin-Installer#readme).
  ```json
  {
    "extra": {
      "dropin-paths": {
        "<wordpress-root/wp-content/>": [
          "package:leocolomb/wp-acorn-cache:dropins/object-cache.php",
          "package:leocolomb/wp-acorn-cache:dropins/advanced-cache.php"
        ]
      }
    }
  }
  ```

* Require the installer and allow its usage.
  ```sh
  composer require koodimonni/composer-dropin-installer
  ```

* Require the package in your Composer-managed WordPress instance.
  ```sh
  composer require leocolomb/wp-acorn-cache
  ```

### Object Cache

The WordPress Object Cache is used to save on trips to the database.
The Object Cache stores all of the cache data to memory and makes the cache
contents available by using a key, which is used to name and later retrieve
the cache contents.

See [WordPress documentation](https://developer.wordpress.org/reference/classes/wp_object_cache/).

#### Driver (recommended)

The cache driver must be setup as per [Laravel documentation](https://laravel.com/docs/cache#configuration).

When using [Bedrock](https://roots.io/bedrock/) as WordPress boilerplate, specify the driver
in your `.env` file.

```dotenv
CACHE_DRIVER=redis
```

#### Configuration (optional)

Object cache behavior can be configured with its appropriate config file, [`config/object-cache.php`](config/object-cache.php).

Start by publishing the configuration file using Acorn.
```bash
wp acorn vendor:publish --provider="LeoColomb\WPAcornCache\Providers\AcornCacheServiceProvider"
```

### Page Cache

With Page Caching, you cache the full output of a page (i.e. the response) and bypass WordPress
entirely on subsequent requests.

**Note:** The page cache is using [Symfony HttpCache](https://symfony.com/doc/current/http_cache.html).
While quite efficient, you should prefer using an appropriate page cache tool, like Varnish, Nginx cache or a CDN.

#### Activation (optional)

Page cache is not activated per default with WordPress.
To enable page cache, define the constant `WP_CACHE` to `true`.

#### Configuration (optional)

Page cache behavior can be configured with its appropriate config file, [`config/page-cache.php`](config/page-cache.php).

Start by publishing the configuration file using Acorn.
```bash
wp acorn vendor:publish --provider="LeoColomb\WPAcornCache\Providers\AcornCacheServiceProvider"
```

## License

ISC © [Léo Colombaro](https://colombaro.fr)
