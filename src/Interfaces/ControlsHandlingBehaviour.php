<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ControlsHandlingBehaviour
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ControlsHandlingBehaviour
{
	public function setUpErrorHandling();

	public function setUpSessionHandling();

	public function setUpEnvironment();

	/**
	 * @param \Exception $exception
	 */
	public function handleUncaughtException( \Exception $exception );
}
