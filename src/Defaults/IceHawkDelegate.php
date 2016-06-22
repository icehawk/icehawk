<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;

/**
 * Class IceHawkDelegate
 * @package Fortuneglobe\IceHawk\Defaults
 */
class IceHawkDelegate implements SetsUpEnvironment
{
	public function setUpErrorHandling( ProvidesRequestInfo $requestInfo )
	{
	}

	public function setUpSessionHandling( ProvidesRequestInfo $requestInfo )
	{
	}

	public function setUpGlobalVars()
	{
	}
}