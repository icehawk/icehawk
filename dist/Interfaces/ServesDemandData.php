<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesDemandData
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesDemandData
{
	/**
	 * @return bool
	 */
	public function isValid();

	/**
	 * @return array
	 */
	public function getValidationMessages();

	/**
	 * @return string
	 */
	public function getDomain();

	/**
	 * @return bool
	 */
	public function isExecutable();
}