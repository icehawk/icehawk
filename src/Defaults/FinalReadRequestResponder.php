<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Class FinalReadRequestResponder
 * @package Fortuneglobe\IceHawk\Defaults
 */
class FinalReadRequestResponder implements RespondsFinallyToReadRequest
{
	public function handleNoResponse( ProvidesReadRequestData $request )
	{
		$requestInfo = $request->getRequestInfo();

		printf(
			"Application did not respond to the %s request: %s",
			$requestInfo->getMethod(),
			$requestInfo->getUri()
		);
	}

	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request )
	{
		throw $throwable;
	}
}