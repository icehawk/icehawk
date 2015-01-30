<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesApiData
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesApiData
{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getVersion();

	/**
	 * @return string
	 */
	public function getIdentifier();
}