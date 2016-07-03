<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface SetsUpEnvironment
 * @package IceHawk\IceHawk\Interfaces
 */
interface SetsUpEnvironment
{
	public function setUpGlobalVars();
	
	public function setUpErrorHandling( ProvidesRequestInfo $requestInfo );

	public function setUpSessionHandling( ProvidesRequestInfo $requestInfo );

}
