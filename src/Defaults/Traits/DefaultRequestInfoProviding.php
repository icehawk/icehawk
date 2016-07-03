<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Defaults\Traits;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Trait DefaultRequestInfoProviding
 * @package IceHawk\IceHawk\Defaults\Traits
 */
trait DefaultRequestInfoProviding
{
	public function getRequestInfo() : ProvidesRequestInfo
	{
		return RequestInfo::fromEnv();
	}
}