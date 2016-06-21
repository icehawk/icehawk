<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Routing\ReadRouter;
use Fortuneglobe\IceHawk\Routing\RouteRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;

class ReadRouterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest(
		ProvidesDestinationInfo $destinationInfo, array $routes, HandlesReadRequest $expectedRequestHandler
	)
	{
		$router = new ReadRouter( $routes );
		$route  = $router->findMatchingRoute( $destinationInfo );

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
	public function testMissingRouteForRequestThrowsException( ProvidesDestinationInfo $requestInfo, array $routes )
	{
		$router = new ReadRouter( $routes );
		$router->findMatchingRoute( $requestInfo );
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
}