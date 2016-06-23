<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use Fortuneglobe\IceHawk\Routing\Patterns\Literal;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Routing\ReadRouter;
use Fortuneglobe\IceHawk\Routing\RouteRequest;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;

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
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
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
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
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