<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\WriteRequestResolver;
use Fortuneglobe\IceHawk\Interfaces\ResolvesWriteRequest;

/**
 * Trait DefaultWriteRequestResolving
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultWriteRequestResolving
{
	public function getWriteRequestResolver() : ResolvesWriteRequest
	{
		return new WriteRequestResolver();
	}
}