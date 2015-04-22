<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\HandlesIceHawkTasks;

/**
 * Class IceHawkDelegate
 *
 * @package Fortuneglobe\IceHawk
 */
class IceHawkDelegate implements HandlesIceHawkTasks
{
	public function configureSession()
	{
		ini_set( 'session.name', 'ihsid' );
		ini_set( 'session.save_handler', 'memcached' );
		ini_set( 'session.save_path', '127.0.0.1:11211' );
		ini_set( 'session.cookie_httponly', true );
		ini_set( 'session.cookie_lifetime', 60 * 60 * 24 );
	}

	public function configureErrorHandling()
	{
		error_reporting( E_ALL );
		ini_set( 'display_errors', 1 );
	}

	/**
	 * @param \Exception $exception
	 *
	 * @throws \Exception
	 */
	public function handleUncaughtException( \Exception $exception )
	{
		throw $exception;
	}
}
