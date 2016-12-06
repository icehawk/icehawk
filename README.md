[![Join the chat at https://gitter.im/icehawk/icehawk](https://badges.gitter.im/icehawk/icehawk.svg)](https://gitter.im/icehawk/icehawk?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
[![Build Status](https://travis-ci.org/icehawk/icehawk.svg?branch=master)](https://travis-ci.org/icehawk/icehawk)
[![Tested PHP versions](https://php-eye.com/badge/icehawk/icehawk/tested.svg)](https://php-eye.com/package/icehawk/icehawk)
[![Coverage Status](https://coveralls.io/repos/github/icehawk/icehawk/badge.svg?branch=master)](https://coveralls.io/github/icehawk/icehawk?branch=master)
[![Latest Stable Version](https://poser.pugx.org/icehawk/icehawk/v/stable)](https://packagist.org/packages/icehawk/icehawk) 
[![Total Downloads](https://poser.pugx.org/icehawk/icehawk/downloads)](https://packagist.org/packages/icehawk/icehawk) 
[![Latest Unstable Version](https://poser.pugx.org/icehawk/icehawk/v/unstable)](https://packagist.org/packages/icehawk/icehawk) 
[![License](https://poser.pugx.org/icehawk/icehawk/license)](https://packagist.org/packages/icehawk/icehawk)

# ![IceHawk Framework](https://icehawk.github.io/images/Logo-Flying-Tail-White.png)

Lightweight PHP routing framework, respecting CQRS. 

## Requirements

 * PHP >= 7.0
 * [fileinfo extension](https://pecl.php.net/package/Fileinfo) for handling uploaded files correctly

**For development only:**

 * [xdebug extension](https://pecl.php.net/package/Xdebug) for running the tests

## Installation

```bash
composer require icehawk/icehawk:^2.0
```

or add to your `composer.json`:

```json
{
	"require": {
		"icehawk/icehawk": "^2.0"
	}
}
```
 
## Documentation

**A full documentation can be found on our website: [icehawk.github.io](https://icehawk.github.io/docs/icehawk.html)**
 
## Quickstart (installer)
 
We provide an installer package that creates a new IceHawk project for you. Simply run:

```bash
composer create-project icehawk/installer /path/to/new-project
```
 
Answer the questions of the interactive installer and you're good to go.

[**&raquo; Watch our short video and see how it works:** Install IceHawk framework in less than 2 minutes](https://youtu.be/ns62lw52AOU)
 
## Quickstart (manual)

### Step 0 - Create a basic composer.json

```json
{
    "require": {
        "icehawk/icehawk": "^2.0"
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
use YourVendor\YourProject\SayHelloRequestHandler;

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
