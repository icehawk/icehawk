<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RespondsFinally
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RespondsFinally
{
	public function handleNotRespondedReadRequest( ProvidesReadRequestData $request );

	public function handleNotRespondedWriteRequest( ProvidesWriteRequestData $request );

	public function handleUncaughtReadException( \Throwable $throwable, ProvidesReadRequestData $request );

	public function handleUncaughtWriteException( \Throwable $throwable, ProvidesWriteRequestData $request );
}