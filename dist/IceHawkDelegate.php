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
	}

	public function configureErrorHandling()
	{
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
