<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Class FinalReadResponder
 * @package IceHawk\IceHawk\Defaults
 */
class FinalReadResponder implements RespondsFinallyToReadRequest
{
	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request )
	{
		throw $throwable;
	}
}