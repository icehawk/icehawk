<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\FinalReadResponder;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Trait DefaultFinalReadResponding
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultFinalReadResponding
{
	public function getFinalReadResponder() : RespondsFinallyToReadRequest
	{
		return new FinalReadResponder();
	}
}