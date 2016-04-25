<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PatchRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use Fortuneglobe\IceHawk\WriteHandlerRouter;

/**
 * Class TestPostRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestWriteRequestResolver implements ResolvesWriteRequest
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
		elseif( $requestInfo->getUri() == '/domain/put' )
		{
			return new WriteHandlerRouter( new PutRequestHandler() );
		}
		elseif( $requestInfo->getUri() == '/domain/patch' )
		{
			return new WriteHandlerRouter( new PatchRequestHandler() );
		}
		elseif( $requestInfo->getUri() == '/domain/delete' )
		{
			return new WriteHandlerRouter( new DeleteRequestHandler() );
		}

		throw ( new UnresolvedRequest() )->withRequestInfo( $requestInfo );
	}
}