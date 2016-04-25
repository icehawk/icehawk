<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;

/**
 * Class TestFinalReadRequestResponder
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestFinalReadRequestResponder implements RespondsFinallyToReadRequest
{
	public function handleNoResponse( ProvidesReadRequestData $request )
	{
		// TODO: Implement handleNoResponse() method.
	}

	public function handleUncaughtException( \Throwable $throwable, ProvidesReadRequestData $request )
	{
		echo get_class( $throwable );
	}
}