<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\ReadRequestResolver;
use Fortuneglobe\IceHawk\Interfaces\ResolvesReadRequest;

/**
 * Trait DefaultReadRequestResolving
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultReadRequestResolving
{
	public function getReadRequestResolver() : ResolvesReadRequest
	{
		return new ReadRequestResolver();
	}
}