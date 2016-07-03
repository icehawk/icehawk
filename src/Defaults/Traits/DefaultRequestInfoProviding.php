<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Trait DefaultRequestInfoProviding
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultRequestInfoProviding
{
	public function getRequestInfo() : ProvidesRequestInfo
	{
		return RequestInfo::fromEnv();
	}
}