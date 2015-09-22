<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ControlsHandlingBehaviour;

/**
 * Class IceHawkDelegate
 *
 * @package Fortuneglobe\IceHawk
 */
class IceHawkDelegate implements ControlsHandlingBehaviour
{
	public function setUpSessionHandling()
	{
	}

	public function setUpErrorHandling()
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
