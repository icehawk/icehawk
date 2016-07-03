<?php
namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Interfaces\HandlesReadRequest;
use IceHawk\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\Patterns\RegExp;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\ReadRouter;
use IceHawk\IceHawk\Routing\RouteRequest;
use IceHawk\IceHawk\Routing\WriteRoute;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;

class ReadRouterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest(
		ProvidesDestinationInfo $routeRequest, array $routes, HandlesReadRequest $expectedRequestHandler
	)
	{
		$router = new ReadRouter( $routes );
		$route  = $router->findMatchingRoute( $routeRequest );

		$this->assertEquals( $expectedRequestHandler, $route->getRequestHandler() );
	}

	public function routeProvider()
	{
		return [
			[
				new RouteRequest( '/test', 'GET' ),
				[
					new ReadRoute( new RegExp( '#^/(unit|test)$#' ), new HeadRequestHandler() ),
					new ReadRoute( new RegExp( '#^/(unit|test)$#' ), new GetRequestHandler() ),
					new ReadRoute( new RegExp( '#^/notvalid$#' ), new HeadRequestHandler() ),
					new ReadRoute( new RegExp( '#^/(invalidToo$#' ), new HeadRequestHandler() ),
				],
				new GetRequestHandler(),
			],
		];
	}

	/**
	 * @dataProvider invalidRouteProvider
	 * @expectedException \IceHawk\IceHawk\Exceptions\UnresolvedRequest
	 */
	public function testMissingRouteForRequestThrowsException( ProvidesDestinationInfo $routeRequest, array $routes )
	{
		$router = new ReadRouter( $routes );
		$router->findMatchingRoute( $routeRequest );
	}

	public function invalidRouteProvider()
	{
		return [
			[
				new RouteRequest( '/test', 'GET' ),
				[
					new ReadRoute( new RegExp( '#^/(unit|test)$#' ), new HeadRequestHandler() ),
					new ReadRoute( new RegExp( '#^/notvalid$#' ), new GetRequestHandler() ),
					new ReadRoute( new RegExp( '#^/invalidToo$#' ), new GetRequestHandler() ),
				],
			],
		];
	}

	/**
	 * @expectedException \IceHawk\IceHawk\Exceptions\UnresolvedRequest
	 */
	public function testReadRouterSkipsRoutesWithWriteRequestHandler()
	{
		$matchingUri = '/test';

		$routes = [
			new WriteRoute( new Literal( $matchingUri ), new PostRequestHandler() )
		];

		$routeRequest = new RouteRequest( $matchingUri, 'POST' );

		$readRouter = new ReadRouter( $routes );
		$readRouter->findMatchingRoute( $routeRequest );
	}
}