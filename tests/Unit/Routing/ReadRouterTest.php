<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Routing\ReadRouter;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;

class ReadRouterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest(
		ProvidesRequestInfo $requestInfo, array $routes, HandlesReadRequest $expectedRequestHandler
	)
	{
		$router = new ReadRouter( $routes );
		$route  = $router->findMatchingRoute( $requestInfo );

		$this->assertEquals( $expectedRequestHandler, $route->getRequestHandler() );
	}

	public function routeProvider()
	{
		return [
			[
				new RequestInfo( [ 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/test' ] ),
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
	public function testMissingRouteForRequestThrowsException( ProvidesRequestInfo $requestInfo, array $routes )
	{
		$router = new ReadRouter( $routes );
		$router->findMatchingRoute( $requestInfo );
	}

	public function invalidRouteProvider()
	{
		return [
			[
				new RequestInfo( [ 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/test' ] ),
				[
					new ReadRoute( new RegExp( '#^/(unit|test)$#' ), new HeadRequestHandler() ),
					new ReadRoute( new RegExp( '#^/notvalid$#' ), new GetRequestHandler() ),
					new ReadRoute( new RegExp( '#^/invalidToo$#' ), new GetRequestHandler() ),
				],
			],
		];
	}
}