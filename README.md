[![CircleCI](https://circleci.com/gh/icehawk/icehawk/tree/3.x-dev.svg?style=svg)](https://circleci.com/gh/icehawk/icehawk/tree/3.x-dev)
[![codecov](https://codecov.io/gh/icehawk/icehawk/branch/3.x-dev/graph/badge.svg)](https://codecov.io/gh/icehawk/icehawk)
[![License](https://poser.pugx.org/icehawk/icehawk/license)](https://packagist.org/packages/icehawk/icehawk)
[![phpstan enabled](https://img.shields.io/badge/phpstan-enabled-green.svg)](https://github.com/phpstan/phpstan)

# ![IceHawk Framework](https://icehawk.github.io/images/Logo-Flying-Tail-White.png)

Lightweight PHP routing framework, respecting CQRS. 

## Requirements

 * PHP >= 7.2
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
 
## Quickstart (installer)
 
We provide an installer package that creates a new IceHawk project for you. Simply run:

```bash
composer create-project -n icehawk/installer /path/to/new-project
```
 
Answer the questions of the interactive installer and you're good to go.

[**&raquo; Watch our short video and see how it works:** Install IceHawk framework in less than 2 minutes](https://youtu.be/ns62lw52AOU)
 
## Quickstart (manual)

### Step 0 - Create a basic composer.json

```json
{
    "require": {
        "icehawk/icehawk": "^2.1"
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

### Step 1 - Create a request handler

```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;

final class SayHelloRequestHandler implements HandlesGetRequest
{
	public function handle( ProvidesReadRequestData $request ) 
	{
		echo "Hello World!";   
	}	
}
```
**`— SayHelloRequestHandler.php`**

### Step 2 - Create a basic config
 
All you need is at least one read or write route.
 
```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\Patterns\Literal;

final class IceHawkConfig extends \IceHawk\IceHawk\Defaults\IceHawkConfig
{
	public function getReadRoutes() 
	{
		return [
			new ReadRoute( new Literal('/'), new SayHelloRequestHandler() ),	
		];
	}
}
```
**`— IceHawkConfig.php`**
 
### Step 3 - Create a bootstrap script

```php
<?php declare(strict_types = 1);

namespace YourVendor\YourProject;

use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Defaults\IceHawkDelegate;

require('vendor/autoload.php');

$iceHawk = new IceHawk(new IceHawkConfig(), new IceHawkDelegate());
$iceHawk->init();

$iceHawk->handleRequest();
```
**`— index.php`**
 
### Step 4 - Say hello

Go to your project folder an run:

```bash
php -S 127.0.0.1:8088
```

Go to your browser an visit: [http://127.0.0.1:8088/](http://127.0.0.1:8088/)

> _Hello World!_


**[Visit our website for the full documentation.](https://icehawk.github.io/docs/icehawk.html)**

## Contributing

Contributions are welcome! Please see our [contribution guide](./CONTRIBUTING.md).
