<?php declare(strict_types = 1);
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Tests;

require(__DIR__ . '/../vendor/autoload.php');

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

class Test
{

}

$className = Test::class;

echo (Test::class instanceof Test) ? 'Yes' : 'No';