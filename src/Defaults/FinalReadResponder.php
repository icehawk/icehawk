<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Class FinalReadResponder
 * @package Fortuneglobe\IceHawk\Defaults
 */
class FinalReadResponder implements RespondsFinallyToReadRequest
{
	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request )
	{
		throw $throwable;
	}
}