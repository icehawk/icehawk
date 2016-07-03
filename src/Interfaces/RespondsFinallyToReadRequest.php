<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface RespondsFinallyToReadRequest
 * @package IceHawk\IceHawk\Interfaces
 */
interface RespondsFinallyToReadRequest
{
	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request );
}