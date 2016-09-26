<?php
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

declare(strict_types = 1);
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
			new WriteRoute( new Literal( $matchingUri ), new PostRequestHandler() ),
		];

		$routeRequest = new RouteRequest( $matchingUri, 'POST' );

		$readRouter = new ReadRouter( $routes );
		$readRouter->findMatchingRoute( $routeRequest );
	}
}
