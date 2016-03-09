<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinally;

/**
 * Class FinalResponder
 * @package Fortuneglobe\IceHawk\Defaults
 */
class FinalResponder implements RespondsFinally
{
	public function handleNotRespondedReadRequest( ProvidesReadRequestData $request )
	{
		echo "Application did not respond to the read request";
	}

	public function handleNotRespondedWriteRequest( ProvidesWriteRequestData $request )
	{
		echo "Application did not respond to the write request";
	}

	public function handleUncaughtReadException( \Throwable $throwable, ProvidesReadRequestData $request )
	{
		throw $throwable;
	}

	public function handleUncaughtWriteException( \Throwable $throwable, ProvidesWriteRequestData $request )
	{
		throw $throwable;
	}
}