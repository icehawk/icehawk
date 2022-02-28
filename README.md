![IceHawk CI PHP 7.4](https://github.com/icehawk/icehawk/workflows/IceHawk%20CI%20PHP%207.4/badge.svg?branch=3.x-dev)
[![codecov](https://codecov.io/gh/icehawk/icehawk/branch/3.x-dev/graph/badge.svg)](https://codecov.io/gh/icehawk/icehawk)
[![License](https://poser.pugx.org/icehawk/icehawk/license)](https://packagist.org/packages/icehawk/icehawk)
[![phpstan enabled](https://img.shields.io/badge/phpstan-enabled-green.svg)](https://github.com/phpstan/phpstan)

# ![IceHawk Framework](https://icehawk.github.io/images/Logo-Flying-Tail-White.png)

Lightweight PHP routing framework, respecting CQRS. 

## Requirements

* PHP >= 8.1
* [fileinfo extension](https://pecl.php.net/package/Fileinfo) for handling uploaded files correctly

**For development only:**

 * [xdebug extension](https://pecl.php.net/package/Xdebug) for running the tests

## Installation

```bash
composer require icehawk/icehawk:v3.0.0-beta
```

or add to your `composer.json`:

```json
{
    "require": {
        "icehawk/icehawk": "v3.0.0-beta"
    }
}
```
 
## Documentation

**A full documentation can be found on our website: [icehawk.github.io](https://icehawk.github.io/docs/icehawk.html)**
 
## Quickstart

### Step 0 - Create a basic composer.json

```json
{
    "require": {
        "icehawk/icehawk": "v3.0.0-beta"
    },
    "autoload": {
        "psr-4": {
            "YourVendor\\YourProject\\": "src/"
        }
    }
}
```

Then run:

```bash
composer update
```

### Step 1 - Create a PSR-15 middleware

**`src/SayHelloMiddleware.php`**

```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\Messages\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SayHelloMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        return Response::new('Hello world!' );
    }	
}
```

### Step 2 - Create an app configuration

**`src/AppConfig.php`**

```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\Interfaces\ConfigInterface;
use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\Routes;
use YourVendor\YourProject\SayHelloMiddleware;

final class AppConfig implements ConfigInterface
{
    public function getAppMiddlewares() : iterable
    {
        return [];
    }

    public function getRoutes() : RoutesInterface
    {
        return Routes::new(
            Route::get('/say/hello', SayHelloMiddleware::class)
        );
    }
}
```

### Step 3 - Create a bootstrap script

... and configure a dependency injection container of your choice

The project ships a register-only container implementing PSR-11.

**`public/index.php`**

```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\Dependencies\Container;
use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Messages\Request;

require('vendor/autoload.php');

$diContainer = Container::new();
$diContainer->register(
    SayHelloMiddleware::class, 
    fn() => new SayHelloMiddleware()
);

$iceHawk = IceHawk::new(new AppConfig(), $diContainer);
$iceHawk->handleRequest(Request::fromGlobals());
```
 
### Step 4 - Say hello

Go to your project folder an run:

```bash
php -S 127.0.0.1:8088 -t public
```

Go to your browser and visit: [http://127.0.0.1:8088/](http://127.0.0.1:8088/)

> _Hello World!_

**[Visit our website for the full documentation.](https://icehawk.github.io/docs/icehawk.html)**

## Contributing

Contributions are welcome! Please see our [contribution guide](./.github/CONTRIBUTING.md).
