<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\Defaults\FinalWriteResponder;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToWriteRequest;

/**
 * Trait DefaultFinalWriteResponding
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultFinalWriteResponding
{
	public function getFinalWriteResponder() : RespondsFinallyToWriteRequest
	{
		return new FinalWriteResponder();
	}
}