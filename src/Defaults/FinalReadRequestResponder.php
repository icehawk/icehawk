<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Class FinalReadRequestResponder
 * @package Fortuneglobe\IceHawk\Defaults
 */
class FinalReadRequestResponder implements RespondsFinallyToReadRequest
{
	public function handleNoResponse( ProvidesReadRequestData $request )
	{
		echo "Application did not respond to the read request";
	}

	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request )
	{
		throw $throwable;
	}
}