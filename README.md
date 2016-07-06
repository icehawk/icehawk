[![Build Status](https://travis-ci.org/icehawk/icehawk.svg?branch=master)](https://travis-ci.org/icehawk/icehawk)
[![Code Climate](https://codeclimate.com/github/icehawk/icehawk/badges/gpa.svg)](https://codeclimate.com/github/icehawk/icehawk)
[![Coverage Status](https://coveralls.io/repos/github/icehawk/icehawk/badge.svg?branch=master)](https://coveralls.io/github/icehawk/icehawk?branch=master)
[![Latest Stable Version](https://poser.pugx.org/icehawk/icehawk/v/stable)](https://packagist.org/packages/icehawk/icehawk) 
[![Total Downloads](https://poser.pugx.org/icehawk/icehawk/downloads)](https://packagist.org/packages/icehawk/icehawk) 
[![Latest Unstable Version](https://poser.pugx.org/icehawk/icehawk/v/unstable)](https://packagist.org/packages/icehawk/icehawk) 
[![License](https://poser.pugx.org/icehawk/icehawk/license)](https://packagist.org/packages/icehawk/icehawk)

# ![IceHawk](icehawk-logo.png)

Lightweight PHP routing framework, respecting CQRS. 

## Requirements

 * PHP >= 7.0
 * [fileinfo extension](https://pecl.php.net/package/Fileinfo) for handling uploaded files correctly
 * [xdebug extension](https://pecl.php.net/package/Xdebug) for running the tests

## Installation

Add this to your `composer.json`:

```json
"require": {
    "icehawk/icehawk": "^2.0"
}
```

To run the tests, you should add this to your `composer.json` too:

```json
"require-dev": {
    "ext-xdebug": "*"
}
```
 
## Contributing

Please see our [contribution guide](./CONTRIBUTING.md).
