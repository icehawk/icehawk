<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface SetsUpEnvironment
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface SetsUpEnvironment
{
	public function setUpErrorHandling();

	public function setUpSessionHandling();

	public function setUpGlobalVars();
}
