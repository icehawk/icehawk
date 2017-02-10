<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Interfaces\HandlesWriteRequest;
use IceHawk\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\Patterns\RegExp;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\RouteRequest;
use IceHawk\IceHawk\Routing\WriteRoute;
use IceHawk\IceHawk\Routing\WriteRouter;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\ValidPostRequestHandler;

class WriteRouterTest extends \PHPUnit\Framework\TestCase
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
	 * @expectedException \IceHawk\IceHawk\Exceptions\UnresolvedRequest
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
	 * @expectedException \IceHawk\IceHawk\Routing\Exceptions\RoutesAreNotTraversable
	 */
	public function testNonTraversableRoutesThrowsException( $invalidRoutes )
	{
		new WriteRouter( $invalidRoutes );
	}

	/**
	 * @expectedException \IceHawk\IceHawk\Exceptions\UnresolvedRequest
	 */
	public function testWriteRouterSkipsRoutesWithReadRequestHandler()
	{
		$matchingUri = '/test';

		$routes = [
			new ReadRoute( new Literal( $matchingUri ), new GetRequestHandler() ),
		];

		$routeRequest = new RouteRequest( $matchingUri, 'POST' );

		$writeRouter = new WriteRouter( $routes );
		$writeRouter->findMatchingRoute( $routeRequest );
	}
}
