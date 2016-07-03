<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use Fortuneglobe\IceHawk\Routing\Patterns\Literal;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Routing\RouteRequest;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Routing\WriteRouter;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\ValidPostRequestHandler;

class WriteRouterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest(
		ProvidesDestinationInfo $routeRequest, array $routes, HandlesWriteRequest $expectedRequestHandler
	)
	{
		$router = new WriteRouter( $routes );
		$route  = $router->findMatchingRoute( $routeRequest );

		$this->assertEquals( $expectedRequestHandler, $route->getRequestHandler() );
	}

	public function routeProvider()
	{
		return [
			[
				new RouteRequest( '/test', 'POST' ),
				[
					new WriteRoute( new RegExp( '#^/(unit|test)$#' ), new PutRequestHandler() ),
					new WriteRoute( new RegExp( '#^/(unit|test)$#' ), new PostRequestHandler() ),
					new WriteRoute( new RegExp( '#^/notvalid$#' ), new ValidPostRequestHandler() ),
					new WriteRoute( new RegExp( '#^/(invalidToo$#' ), new IceHawkWriteRequestHandler() ),
				],
				new PostRequestHandler(),
			],
		];
	}

	/**
	 * @dataProvider invalidRouteProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
	 */
	public function testMissingRouteForRequestThrowsException( ProvidesDestinationInfo $routeRequest, array $routes )
	{
		$router = new WriteRouter( $routes );
		$router->findMatchingRoute( $routeRequest );
	}

	public function invalidRouteProvider()
	{
		return [
			[
				new RouteRequest( '/test', 'POST' ),
				[
					new WriteRoute( new RegExp( '#^/(unit|test)$#' ), new PutRequestHandler() ),
					new WriteRoute( new RegExp( '#^/notvalid$#' ), new ValidPostRequestHandler() ),
					new WriteRoute( new RegExp( '#^/invalidToo$#' ), new IceHawkWriteRequestHandler() ),
				],
			],
		];
	}

	public function invalidRoutesProvider()
	{
		return [
			[ 'string' ], [ 1 ], [ 0.0 ], [ false ], [ true ], [ new \stdClass() ], [ null ],
		];
	}

	/**
	 * @dataProvider invalidRoutesProvider
	 * @expectedException \Fortuneglobe\IceHawk\Routing\Exceptions\RoutesAreNotTraversable
	 */
	public function testNonTraversableRoutesThrowsException( $invalidRoutes )
	{
		new WriteRouter( $invalidRoutes );
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
	 */
	public function testWriteRouterSkipsRoutesWithReadRequestHandler()
	{
		$matchingUri = '/test';

		$routes = [
			new ReadRoute( new Literal( $matchingUri ), new GetRequestHandler() )
		];

		$routeRequest = new RouteRequest( $matchingUri, 'POST' );

		$writeRouter = new WriteRouter( $routes );
		$writeRouter->findMatchingRoute( $routeRequest );
	}
}