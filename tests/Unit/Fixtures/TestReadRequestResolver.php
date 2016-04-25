<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\ReadHandlerRouter;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\IceHawkReadRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\ValidReadTestRequestHandler;

/**
 * Class TestGetRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestReadRequestResolver implements ResolvesReadRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToReadHandler
	{
		if( $requestInfo->getUri() == '/domain/ice_hawk_read' )
		{
			return new ReadHandlerRouter( new IceHawkReadRequestHandler() );
		}
		elseif( $requestInfo->getUri() ==  '/domain/valid_read_test' )
		{
			return new ReadHandlerRouter( new ValidReadTestRequestHandler() );
		}
		elseif( $requestInfo->getUri() ==  '/domain/get' )
		{
			return new ReadHandlerRouter( new GetRequestHandler() );
		}
		elseif( $requestInfo->getUri() ==  '/domain/head' )
		{
			return new ReadHandlerRouter( new HeadRequestHandler() );
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}
}