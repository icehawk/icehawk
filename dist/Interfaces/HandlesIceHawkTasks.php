<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesIceHawkTasks
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface HandlesIceHawkTasks
{
	public function configureSession();

	public function configureErrorHandling();

	/**
	 * @param \Exception $exception
	 */
	public function handleUncaughtException( \Exception $exception );
}
