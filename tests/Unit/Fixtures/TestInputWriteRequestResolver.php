<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\BodyDataRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\RequestParamsRequestHandler;
use Fortuneglobe\IceHawk\WriteHandlerRouter;

/**
 * Class TestBodyDataWriteRequestResolver
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestInputWriteRequestResolver implements ResolvesWriteRequest
{
	public function resolve( ProvidesRequestInfo $requestInfo ) : RoutesToWriteHandler
	{
		if ( $requestInfo->getUri() == '/domain/test_body_data' )
		{
			return new WriteHandlerRouter( new BodyDataRequestHandler() );
		}
		else
		{
			$requestUri = strtolower( $requestInfo->getUri() );
			$uriParams  = [ ];
			
			if ( preg_match( '!^\/domain\/test_request_param\/(?<key>.*)\/(?<value>.*)!', $requestUri, $matches	) )
			{
				$uriParams = [ $matches['key'] => $matches['value'] ]; 
			}

			return new WriteHandlerRouter( new RequestParamsRequestHandler(), $uriParams );
		}
	}
}