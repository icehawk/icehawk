![IceHawk CI PHP 7.4](https://github.com/icehawk/icehawk/workflows/IceHawk%20CI%20PHP%207.4/badge.svg?branch=3.x-dev)
[![codecov](https://codecov.io/gh/icehawk/icehawk/branch/3.x-dev/graph/badge.svg)](https://codecov.io/gh/icehawk/icehawk)
[![License](https://poser.pugx.org/icehawk/icehawk/license)](https://packagist.org/packages/icehawk/icehawk)
[![phpstan enabled](https://img.shields.io/badge/phpstan-enabled-green.svg)](https://github.com/phpstan/phpstan)

# ![IceHawk Framework](https://icehawk.github.io/images/Logo-Flying-Tail-White.png)

Lightweight PHP routing framework, respecting CQRS. 

## Requirements

 * PHP >= 7.4
 * [fileinfo extension](https://pecl.php.net/package/Fileinfo) for handling uploaded files correctly

**For development only:**

 * [xdebug extension](https://pecl.php.net/package/Xdebug) for running the tests

## Installation

```bash
composer require icehawk/icehawk:~3.0
```

or add to your `composer.json`:

```json
{
	"require": {
		"icehawk/icehawk": "~3.0"
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
        "icehawk/icehawk": "~3.0"
    },
    "autoload": {
        "psr-4": {
            "YourVendor\\YourProject\\": "./"
        }
    }
}
```

Then run:
 
```bash
composer update
```

### Step 1 - Create a PSR-15 middleware

```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Messages\Stream;use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SayHelloMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        return Response::new()->withBody( Stream::newWithContent('Hello world!') );
    }	
}
```
**`— SayHelloMiddleware.php`**

### Step 2 - Create a dependency injection container
 
```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use IceHawk\IceHawk\Routing\Route;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Types\MiddlewareClassName;
use IceHawk\IceHawk\Types\RequestHandlerClassName;
use LogicException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use YourVendor\YourProject\SayHelloMiddleware;

final class Dependencies implements ResolvesDependencies
{
    public function getRoutes() : Routes
    {
        return Routes::new(
            Route::get('/say/hello', SayHelloMiddleware::class)
        );
    }

    public function resolveRequestHandler(RequestHandlerClassName $handlerClassName, MiddlewareClassName ...$middlewareClassNames) : RequestHandlerInterface
    {
        $requestHandler = QueueRequestHandler::newWithFallbackHandler(
            FallbackRequestHandler::newWithMessage( 'Could not find any middleware.' )
        );
        
        foreach ( $middlewareClassNames as $middlewareClassName )
        {   
            $requestHandler->add( $this->resolveMiddleware( $middlewareClassName ) );
        }
    
        return $requestHandler;
    }

    private function resolveMiddleware(MiddlewareClassName $middlewareClassName) : MiddlewareInterface
    {
        switch (true)
        {
            case $middlewareClassName->equalsString(SayHelloMiddleware::class):
                return new SayHelloMiddleware();

            default:
                throw new LogicException('Missing implementation for middleware: ' . $middlewareClassName->toString());
        }
    }
}
```
**`— Dependencies.php`**
 
### Step 3 - Create a bootstrap script

```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Messages\Request;

require('vendor/autoload.php');

$iceHawk = IceHawk::newWithDependencies(new Dependencies());
$iceHawk->handleRequest(Request::fromGlobals());
```
**`— index.php`**
 
### Step 4 - Say hello

Go to your project folder an run:

```bash
php -S 127.0.0.1:8088
```

Go to your browser and visit: [http://127.0.0.1:8088/](http://127.0.0.1:8088/)

> _Hello World!_


**[Visit our website for the full documentation.](https://icehawk.github.io/docs/icehawk.html)**

## Contributing

Contributions are welcome! Please see our [contribution guide](./.github/CONTRIBUTING.md).
