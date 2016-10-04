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

use IceHawk\IceHawk\Routing\Patterns\NamedRegExp;
use IceHawk\IceHawk\Routing\Patterns\RegExp;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\ReadRouteGroup;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\AnotherGetRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\IceHawkReadRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\ValidGetRequestHandler;

class ReadRouteGroupTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest( ReadRouteGroup $groupedRoute, string $uri, $expectedRequestHandler )
	{
		$groupedRoute->matches( $uri );

		$this->assertEquals( $expectedRequestHandler, $groupedRoute->getRequestHandler() );
	}

	public function routeProvider()
	{
		return [
			[
				#Route through part-matching routes to finally find the exactly matching route (first of last route-list)
				new ReadRouteGroup(
					new RegExp( '#^/companies#' ),
					[
						new ReadRouteGroup(
							new RegExp( '#^/companies/stores#' ),
							[
								new ReadRoute(
									new RegExp( '#^/companies/stores/(?<storeId>\d*)#' ),
									new IceHawkReadRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks#' ),
									new ValidGetRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/123',
				new IceHawkReadRequestHandler(),
			],
			#Route through part-matching routes to finally find the exactly matching route (last of last route-list)
			[
				new ReadRouteGroup(
					new RegExp( '#^/companies#' ),
					[
						new ReadRouteGroup(
							new RegExp( '#^/companies/stores#' ),
							[
								new ReadRoute(
									new RegExp( '#^/companies/stores/(?<storeId>\d*)$#' ),
									new ValidGetRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks$#' ),
									new AnotherGetRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks',
				new AnotherGetRequestHandler(),
			],
			[
				new ReadRouteGroup(
					new RegExp( '#^/companies#' ),
					[
						new ReadRouteGroup(
							new RegExp( '#^/companies/stores#' ),
							[
								new ReadRoute(
									new RegExp( '#^/companies/stores/(?<storeId>\d*)#' ),
									new ValidGetRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stock$#' ),
									new AnotherGetRequestHandler()
								),
							]
						),
						new ReadRoute(
							new RegExp( '#^/companies$#' ),
							new GetRequestHandler()
						),
					]
				),
				'/companies',
				new GetRequestHandler(),
			],
			#Use only ReadRouteGroups to route through part-matching routes to a non existing route
			[
				new ReadRouteGroup(
					new RegExp( '#^/companies#' ),
					[
						new ReadRouteGroup(
							new RegExp( '#^/companies/stores#' ),
							[
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks/(?<stockId>\d*)$#' ),
									new ValidGetRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks$#' ),
									new AnotherGetRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks/doesnotexist',
				null,
			],
			#Use Regex to finish on first route with negative match result
			[
				new ReadRouteGroup(
					new RegExp( '#^/companies#' ),
					[
						new ReadRouteGroup(
							new RegExp( '#^/companies/stores#' ),
							[
								new ReadRoute(
									new RegExp( '#^/companies/stores/(?<storeId>\d*)#' ),
									new ValidGetRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks#' ),
									new AnotherGetRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores',
				null,
			],
			#add matching Route after a non matching Route doesn't match
			[
				new ReadRouteGroup(
					new RegExp( '#^/companies#' ),
					[
						new ReadRouteGroup(
							new RegExp( '#^/companies/stores#' ),
							[
								new ReadRoute(
									new RegExp( '#^/companies/stores/(?<storeId>\d*)#' ),
									new ValidGetRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stocks#' ),
									new AnotherGetRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stocks',
				null,
			],
		];
	}

	public function testAddingRoutes()
	{
		$expectedRequestHandler = new AnotherGetRequestHandler();

		$companyGroup = new ReadRouteGroup( new RegExp( '#^/companies#' ) );
		$storeGroup   = new ReadRouteGroup( new RegExp( '#^/companies/stores#' ) );

		$storeGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/stores$#' ), new ValidGetRequestHandler() )
		);

		$storeGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/stores/store#' ), new HeadRequestHandler() )
		);

		$storeGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/stores/stocks#' ), new AnotherGetRequestHandler() )
		);

		$companyGroup->addRoute( $storeGroup );

		$companyGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/members#' ), new IceHawkReadRequestHandler() )
		);

		$result = $companyGroup->matches( '/companies/stores/stocks' );

		$this->assertTrue( $result );
		$this->assertEquals( $expectedRequestHandler, $companyGroup->getRequestHandler() );
	}

	public function testReadUriParamsAreBuiltFromGroupAndMatchingRoute()
	{
		$groupPattern   = new NamedRegExp( '^/group/(?<groupMatch>[0-9]+)/' );
		$subPattern     = new NamedRegExp( '^/group/[0-9]+/(?<subMatch>[a-z]+)' );
		$requestHandler = new ValidGetRequestHandler();
		$routeGroup     = new ReadRouteGroup(
			$groupPattern,
			[
				new ReadRoute( $subPattern, $requestHandler ),
			]
		);

		$expectedUriParams = [
			'groupMatch' => '0815',
			'subMatch'   => 'test',
		];

		$result = $routeGroup->matches( '/group/0815/test' );

		$this->assertTrue( $result );
		$this->assertSame( $requestHandler, $routeGroup->getRequestHandler() );
		$this->assertEquals( $expectedUriParams, $routeGroup->getUriParams() );
	}
}
