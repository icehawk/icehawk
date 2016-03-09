<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesRequestData
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ProvidesRequestData
{
	public function getData() : array;

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( string $key );

	public function getRequestInfo() : ProvidesRequestInfo;
} 