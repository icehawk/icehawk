<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RespondsFinallyToWriteRequest
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RespondsFinallyToWriteRequest
{
	public function handleNoResponse( ProvidesWriteRequestData $request );

	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request );
}