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
	public function setUpGlobalVars();
	
	public function setUpErrorHandling( ProvidesRequestInfo $requestInfo );

	public function setUpSessionHandling( ProvidesRequestInfo $requestInfo );

}
