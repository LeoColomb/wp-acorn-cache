# Acorn Cache for WordPress

> A cache manager powered by Laravel through Acorn.  

[![Build Status](https://github.com/LeoColomb/wp-acorn-cache/workflows/PHP%20CI/badge.svg)](https://github.com/LeoColomb/wp-acorn-cache/actions?query=workflow%3APHP%20CI)
[![Packagist](https://img.shields.io/packagist/v/LeoColomb/wp-acorn-cache.svg)](https://packagist.org/packages/LeoColomb/wp-acorn-cache)

## Features

* Enables the two cache wrappers for WordPress
  * Object Cache ([`object-cache.php`](dropins/object-cache.php))
  * Advanced Page Cache ([`advanced-cache.php`](dropins/advanced-cache.php))
* Adds handy [WP-CLI](https://wp-cli.org/) commands.
* Targets modern software stacks.


## Installation

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
  $ composer require leocolomb/wp-acorn-cache
  ```


## License

ISC © [Léo Colombaro](https://colombaro.fr)
