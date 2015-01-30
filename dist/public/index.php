<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

require_once __DIR__ . '/../../vendor/autoload.php';

use Fortuneglobe\IceHawk\Responses\Page;

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

$icehawk = IceHawk::fromSky();
$icehawk->init();

$request_handler = $icehawk->getRequestHandler();
$request_handler->handle();

$page = new Page( 'Test.twig', [ 'world' => 'Welt' ] );
$page->respond();