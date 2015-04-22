<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

require_once __DIR__ . '/../../vendor/autoload.php';

error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

$iceHawk = new IceHawk( new IceHawkConfig(), new IceHawkDelegate() );
$iceHawk->init();
$iceHawk->handleRequest();
