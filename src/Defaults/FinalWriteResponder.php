<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;

/**
 * Class FinalWriteResponder
 * @package IceHawk\IceHawk\Defaults
 */
class FinalWriteResponder implements RespondsFinallyToWriteRequest
{
	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request )
	{
		throw $throwable;
	}
}