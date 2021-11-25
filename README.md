
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/Fabricio872/api-modeller-bundle)
![GitHub last commit](https://img.shields.io/github/last-commit/Fabricio872/api-modeller-bundle)
[![PHP Composer Test and Tag](https://github.com/Fabricio872/api-modeller-bundle/actions/workflows/php-composer.yml/badge.svg)](https://github.com/Fabricio872/api-modeller-bundle/actions/workflows/php-composer.yml)
![Packagist Downloads](https://img.shields.io/packagist/dt/Fabricio872/api-modeller)
![GitHub Repo stars](https://img.shields.io/github/stars/Fabricio872/api-modeller-bundle?style=social)

Valuable partners:

![PhpStorm logo](https://resources.jetbrains.com/storage/products/company/brand/logos/PhpStorm.svg)

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require fabricio872/api-modeller
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require fabricio872/api-modeller
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Fabricio872\ApiModeller\ApiModellerBundle::class => ['all' => true],
];
```
