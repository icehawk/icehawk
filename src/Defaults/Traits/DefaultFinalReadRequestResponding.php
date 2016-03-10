<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\FinalReadRequestResponder;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Trait DefaultFinalReadRequestResponding
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultFinalReadRequestResponding
{
	public function getFinalReadRequestResponder() : RespondsFinallyToReadRequest
	{
		return new FinalReadRequestResponder();
	}
}