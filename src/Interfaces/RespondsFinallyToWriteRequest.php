<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface RespondsFinallyToWriteRequest
 * @package IceHawk\IceHawk\Interfaces
 */
interface RespondsFinallyToWriteRequest
{
	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request );
}