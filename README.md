# The IceHawk framework

Fast and reliable PHP frontend framework using CQRS.

## Installation

Add this to your `composer.json`:

```json
require: {
    "fortuneglobe/icehawk": "1.3.0"
}
```

## Get started

### 0. Preamble

 * We will use `Vendor\Project` as the example namespace, replace this to fit your project.
 * We will use the following structure in the file system for our examples:
 
```
- Project
  |- src				# Root of your PSR-0 namespace (Vendor\Project)
  |  |- Domains			# Root folder for domains (where you'll put your request handlers)
  |  `- public			# The webserver's document root
  |     `- index.php	# The bootstrap file
  |- vendor				# genereated by composer
  `- composer.json
```
	
### 1. Basic configuration

* Create a config class that extends the default config class and serves your project namespace.
* Put this file to: `src/MyIceHawkConfig.php`

```php
<?php

namespace Vendor\Project;

use Fortuneglobe\IceHawk\IceHawkConfig;

final class MyIceHawkConfig extends IceHawkConfig
{
	/**
	 * @return string
	 */
	public function getDomainNamespace()
	{
		return __NAMESPACE__ . '\\Domains';
	}
}
```

### 2. Init

* Create a bootstrap file in your document root with the following content.
* Put this file to: `src/public/index.php`
 
```php
<?php

namespace Vendor\Project;

use Fortuneglobe\IceHawk\IceHawk;
use Fortuneglobe\IceHawk\IceHawkDelegate;

require(__DIR__ . '/../../vendor/autoload.php');

$iceHawk = new IceHawk( new MyIceHawkConfig(), new IceHawkDelegate() );
$iceHawk->init();

$iceHawk->handleRequest();
```