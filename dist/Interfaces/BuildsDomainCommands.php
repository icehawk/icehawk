<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface BuildsDomainCommands
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface BuildsDomainCommands
{
	/**
	 * @param ServesRequestData $request
	 *
	 * @return ServesCommandData
	 */
	public function buildCommand( ServesRequestData $request );
}