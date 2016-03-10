<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToWriteRequest;

/**
 * Class FinalWriteRequestResponder
 * @package Fortuneglobe\IceHawk\Defaults
 */
class FinalWriteRequestResponder implements RespondsFinallyToWriteRequest
{
	public function handleNoResponse( ProvidesWriteRequestData $request )
	{
		echo "Application did not respond to the read request";
	}

	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request )
	{
		throw $throwable;
	}
}