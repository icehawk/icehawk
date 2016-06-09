<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToWriteRequest;

/**
 * Class FinalWriteResponder
 * @package Fortuneglobe\IceHawk\Defaults
 */
class FinalWriteResponder implements RespondsFinallyToWriteRequest
{
	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request )
	{
		throw $throwable;
	}
}