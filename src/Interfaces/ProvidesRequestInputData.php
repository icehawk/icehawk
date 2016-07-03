<?php
namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ProvidesRequestInputs
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesRequestInputData
{
	public function getData() : array;

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( string $key );
}