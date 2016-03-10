<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RespondsFinallyToReadRequest
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RespondsFinallyToReadRequest
{
	public function handleNoResponse( ProvidesReadRequestData $request );

	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request );
}