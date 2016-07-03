<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\SetsUpEnvironment;

/**
 * Class IceHawkDelegate
 * @package IceHawk\IceHawk\Defaults
 */
class IceHawkDelegate implements SetsUpEnvironment
{
	public function setUpGlobalVars()
	{
	}
	
	public function setUpErrorHandling( ProvidesRequestInfo $requestInfo )
	{
	}

	public function setUpSessionHandling( ProvidesRequestInfo $requestInfo )
	{
	}

}