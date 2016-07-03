<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Defaults\Traits;

use IceHawk\IceHawk\Defaults\FinalReadResponder;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Trait DefaultFinalReadResponding
 * @package IceHawk\IceHawk\Defaults\Traits
 */
trait DefaultFinalReadResponding
{
	public function getFinalReadResponder() : RespondsFinallyToReadRequest
	{
		return new FinalReadResponder();
	}
}