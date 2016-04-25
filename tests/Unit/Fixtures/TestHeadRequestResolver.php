<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\ReadHandlerRouter;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;

/**
 * Class TestHeadRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestHeadRequestResolver implements ResolvesReadRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToReadHandler
	{
		if( $requestInfo->getUri() == '/domain/head' )
		{
			return new ReadHandlerRouter( new HeadRequestHandler() );
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}

}