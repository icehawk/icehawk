<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Defaults\Traits;

use IceHawk\IceHawk\Defaults\FinalWriteResponder;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;

/**
 * Trait DefaultFinalWriteResponding
 * @package IceHawk\IceHawk\Defaults\Traits
 */
trait DefaultFinalWriteResponding
{
	public function getFinalWriteResponder() : RespondsFinallyToWriteRequest
	{
		return new FinalWriteResponder();
	}
}