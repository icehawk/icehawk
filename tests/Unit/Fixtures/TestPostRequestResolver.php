<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\WriteHandlerRouter;

/**
 * Class TestWriteRequestHandler
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestPostRequestResolver implements ResolvesWriteRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToWriteHandler
	{
		if( $requestInfo->getUri() == '/domain/ice_hawk_write' )
		{
			return new WriteHandlerRouter( new IceHawkWriteRequestHandler() );
		}
		elseif( $requestInfo->getUri() == '/domain/post' )
		{
			return new WriteHandlerRouter( new PostRequestHandler() );
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}
}