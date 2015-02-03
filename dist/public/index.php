<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

$icehawk = IceHawk::fromSky( new IceHawkDelegate() );
$icehawk->init();

$request_handler = $icehawk->getRequestHandler();
$request_handler->handle();
