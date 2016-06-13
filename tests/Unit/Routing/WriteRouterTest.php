<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Routing\WriteRouter;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\ValidWriteTestRequestHandler;

class WriteRouterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest(
		ProvidesRequestInfo $requestInfo, array $routes, HandlesWriteRequest $expectedRequestHandler
	)
	{
		$router = new WriteRouter( $routes );
		$route  = $router->findMatchingRoute( $requestInfo );

		$this->assertEquals( $expectedRequestHandler, $route->getRequestHandler() );
	}

	public function routeProvider()
	{
		return [
			[
				new RequestInfo( [ 'REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/test' ] ),
				[
					new WriteRoute( new RegExp( '#^/(unit|test)$#' ), new PutRequestHandler() ),
					new WriteRoute( new RegExp( '#^/(unit|test)$#' ), new PostRequestHandler() ),
					new WriteRoute( new RegExp( '#^/notvalid$#' ), new ValidWriteTestRequestHandler() ),
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
	public function testMissingRouteForRequestThrowsException( ProvidesRequestInfo $requestInfo, array $routes )
	{
		$router = new WriteRouter( $routes );
		$router->findMatchingRoute( $requestInfo );
	}

	public function invalidRouteProvider()
	{
		return [
			[
				new RequestInfo( [ 'REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/test' ] ),
				[
					new WriteRoute( new RegExp( '#^/(unit|test)$#' ), new PutRequestHandler() ),
					new WriteRoute( new RegExp( '#^/notvalid$#' ), new ValidWriteTestRequestHandler() ),
					new WriteRoute( new RegExp( '#^/invalidToo$#' ), new IceHawkWriteRequestHandler() ),
				]
			],
		];
	}
}