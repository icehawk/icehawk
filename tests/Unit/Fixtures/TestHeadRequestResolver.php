<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\ReadHandlerRouter;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\IceHawkReadRequestHandler;

/**
 * Class TestHeadRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestHeadRequestResolver implements ResolvesWriteRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToWriteHandler
	{
		if( $requestInfo->getUri() == '/domain/head' )
		{
			return new ReadHandlerRouter( new IceHawkReadRequestHandler() );
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}

}