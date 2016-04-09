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
		$requestInfo = $request->getRequestInfo();

		printf(
			"Application did not respond to the %s request: %s",
			$requestInfo->getMethod(),
			$requestInfo->getUri()
		);
	}

	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request )
	{
		throw $throwable;
	}
}