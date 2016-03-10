<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\FinalWriteRequestResponder;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToWriteRequest;

/**
 * Trait DefaultFinalWriteRequestResponding
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultFinalWriteRequestResponding
{
	public function getFinalWriteRequestResponder() : RespondsFinallyToWriteRequest
	{
		return new FinalWriteRequestResponder();
	}
}