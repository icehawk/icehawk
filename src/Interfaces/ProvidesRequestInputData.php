<?php
namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ProvidesRequestInputs
 *
 * @package Fortuneglobe\IceHawk\Interfaces
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