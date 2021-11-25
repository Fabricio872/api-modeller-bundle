
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

# Usage
> This bundle uses models with Annotations similar to Doctrine Entities.
>
> Usually they are in a directory src/ApiModels but they are not required to be there as long as they have correct namespace

## Example model with single Resource

This is example of a model for receiving list of users from some API

```php
// src/ApiModels/Users.php

use Fabricio872\ApiModeller\Annotations\Resource;

/**
 * @Resource(
 *     endpoint="{{api_url}}/api/users",
 *     method="GET",
 *     type="json",
 *     options={
 *          "headers"={
 *              "accept"= "application/json"
 *          }
 *     }
 * )
 */
class Users
{
    public $page;
    public $per_page;
    public $total;
    public $total_pages;
    public $data;
}
```

> endpoint parameter is endpoint which will be called.
>
> The variable {{api_url}} is Twig global variable configured in twig config (example in [Global variables configuration](#global-variables-configuration) section)

> method parameter is method with which the request will be done 
> 
> default: "GET"

> type parameter defines format of the received data 
> 
> currently supported: "json", "xml'
> 
> default: "json"

> options parameter is array that is directly passed (but can be altered as explained in [setOptions](#setoptions) section) 
> to [symfony/http-client](https://github.com/symfony/http-client) request method as 3. parameter so use [this documentation](https://symfony.com/doc/current/http_client.html) 

## Example model with multiple Resources
To define multiple resources you need to wrap multiple Resource annotation into single Resources annotation with identifier at beginning.
This identifier is then used while calling this endpoint as described in section [setIdentifier](#setidentifier)

```php
// src/ApiModels/Users.php

use Fabricio872\ApiModeller\Annotations\Resource;
use Fabricio872\ApiModeller\Annotations\Resources;

/**
 * @Resources({
 *      "multiple"= @Resource(
 *          endpoint="{{api_url}}/api/users",
 *          method="GET",
 *          type="json",
 *          options={
 *              "headers"={
 *                  "accept"= "application/json"
 *              }
 *          }
 *      ),
 *      "single"= @Resource(
 *          endpoint="{{api_url}}/api/users/{{id}}",
 *          method="GET",
 *          type="json",
 *          options={
 *              "headers"={
 *                  "accept"= "application/json"
 *              }
 *          }
 *      ),
 * })
 */
class Users
{
    public $page;
    public $per_page;
    public $total;
    public $total_pages;
    public $data;
}
```



## Global variables configuration
```yaml
# config/packages/twig.yaml

twig:
    ...
    globals:
        api_url: 'https://reqres.in'
```

## Calling the API

> This bundle can be initialized from symphony's dependency container so in your controller you can call it like this:

This controller dumps model or collection of models form [this example](#example-model-with-single-resource) with namespace Users::class
and sets query parameter 'page' to 2
```php
// src/Controller/SomeController.php


    /**
     * @Route("/", name="some_controller")
     */
    public function index(Modeller $modeller): Response
    {
        dump($modeller->getData(
            Repo::new(Users::class)
                ->setOptions([
                    "query" => [
                        "page" => 2
                    ]
                ])
        ));

        return $this->render('some_controller/index.html.twig', [
            'controller_name' => 'SomeController',
        ]);
    }
```

This controller dumps model or collection of models form [this example](#example-model-with-multiple-resources) with namespace Users::class
and fills the {{id}} variable from model with number 2

noticed that now method setIdentifier is required
```php
// src/Controller/SomeController.php


    /**
     * @Route("/", name="some_controller")
     */
    public function index(Modeller $modeller): Response
    {
        dump($modeller->getData(
            Repo::new(Users::class)
                ->setParameters([
                    "id" => 2
                ])
                ->setIdentifier("single")
        ));

        return $this->render('some_controller/index.html.twig', [
            'controller_name' => 'SomeController',
        ]);
    }
```

> The modeller accepts Repo object which requires namespace of model you want to build
> and has optional setters:
> - setOptions()
> - setParameters()
> - setIdentifier()

### setOptions
This method accepts array of options that will be merged with options configured in a model (and will override overlapped parameters) 
to [symfony/http-client](https://github.com/symfony/http-client) request method as 3. parameter so use [this documentation](https://symfony.com/doc/current/http_client.html) 

### setParameters
This method accepts array and sets twig variables (same as if you render a template but here the template is endpoint parameter
from model) to url configuration and can override global twig variables

### setIdentifier
This method is required in case when you use multiple Resources for single model as shown in [this example]()

