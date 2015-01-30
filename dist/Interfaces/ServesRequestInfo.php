<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesRequestInfo
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesRequestInfo
{
	/**
	 * @return string
	 */
	public function getMethod();

	/**
	 * @return string
	 */
	public function getUri();

	/**
	 * @return string
	 */
	public function getServerName();
}