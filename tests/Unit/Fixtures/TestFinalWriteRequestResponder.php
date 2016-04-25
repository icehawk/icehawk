<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToWriteRequest;

/**
 * Class TestFinalWriteRequestResponder
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestFinalWriteRequestResponder implements RespondsFinallyToWriteRequest
{
	public function handleNoResponse( ProvidesWriteRequestData $request )
	{
		// TODO: Implement handleNoResponse() method.
	}

	public function handleUncaughtException( \Throwable $throwable, ProvidesWriteRequestData $request )
	{
		echo get_class( $throwable );	
	}
}