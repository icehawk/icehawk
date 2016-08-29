<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesRequestData
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesRequestData
{
	public function getRequestInfo() : ProvidesRequestInfo;

	/**
	 * @param string $key
	 * @param null|string|array $default
	 *
	 * @return null|string|array
	 */
	public function get( string $key, $default );

	public function getInputData() : array;
} 