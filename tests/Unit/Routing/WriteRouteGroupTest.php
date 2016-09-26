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

use IceHawk\IceHawk\Routing\Patterns\RegExp;
use IceHawk\IceHawk\Routing\WriteRoute;
use IceHawk\IceHawk\Routing\WriteRouteGroup;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\AnotherPostRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\BodyDataRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PatchRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\ValidPostRequestHandler;

class WriteRouteGroupTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest( WriteRouteGroup $groupedRoute, string $uri, $expectedRequestHandler )
	{
		$groupedRoute->matches( $uri );

		$this->assertEquals( $expectedRequestHandler, $groupedRoute->getRequestHandler() );
	}

	public function routeProvider()
	{
		return [
			[
				#Route through part-matching routes to finally find the exactly matching route (first of last route-list)
				new WriteRouteGroup(
					new RegExp( '!/companies!' ),
					[
						new WriteRouteGroup(
							new RegExp( '!/companies/stores!' ),
							[
								new WriteRoute(
									new RegExp( '!/companies/stores/(?<storeId>\d*)$!' ),
									new ValidPostRequestHandler()
								),
								new WriteRoute(
									new RegExp( '!/companies/stores/stocks$!' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/123',
				new ValidPostRequestHandler(),
			],
			#Route through part-matching routes to finally find the exactly matching route (last of last route-list)
			[
				new WriteRouteGroup(
					new RegExp( '!/companies!' ),
					[
						new WriteRouteGroup(
							new RegExp( '!/companies/stores!' ),
							[
								new WriteRoute(
									new RegExp( '!/companies/stores/(?<storeId>\d*)$!' ),
									new ValidPostRequestHandler()
								),
								new WriteRoute(
									new RegExp( '!/companies/stores/stocks$!' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks',
				new IceHawkWriteRequestHandler(),
			],
			[
				new WriteRouteGroup(
					new RegExp( '!/companies!' ),
					[
						new WriteRoute( new RegExp( '!/companies$!' ), new PostRequestHandler() ),
						new WriteRouteGroup(
							new RegExp( '!/companies/stores!' ),
							[
								new WriteRoute(
									new RegExp( '!/companies/stores/(?<storeId>\d*)$!' ),
									new ValidPostRequestHandler()
								),
								new WriteRoute(
									new RegExp( '!/companies/stores/stock$!' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies',
				new PostRequestHandler(),
			],
			#Use only WriteRouteGroups to route through part-matching routes to a non existing route
			[
				new WriteRouteGroup(
					new RegExp( '!/companies!' ),
					[
						new WriteRouteGroup(
							new RegExp( '!/companies/stores!' ),
							[
								new WriteRoute(
									new RegExp( '!/companies/stores/stocks/(?<stockId>\d*)$!' ),
									new ValidPostRequestHandler()
								),
								new WriteRoute(
									new RegExp( '!/companies/stores/stocks$!' ),
									new IceHawkWriteRequestHandler()
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
				new WriteRouteGroup(
					new RegExp( '!/companies$!' ),
					[
						new WriteRouteGroup(
							new RegExp( '!/companies/stores!' ),
							[
								new WriteRoute(
									new RegExp( '!/companies/stores/(?<storeId>\d*)$!' ),
									new ValidPostRequestHandler()
								),
								new WriteRoute(
									new RegExp( '!/companies/stores/stocks!' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores',
				null,
			],
			[
				new WriteRouteGroup(
					new RegExp( '!/companies!' ),
					[
						new WriteRoute( new RegExp( '!^/companies/stores$!' ), new DeleteRequestHandler() ),
						new WriteRouteGroup(
							new RegExp( '!/companies/stores!' ),
							[
								new WriteRoute(
									new RegExp( '!/companies/stores/(?<storeId>\d*$)!' ),
									new ValidPostRequestHandler()
								),
								new WriteRoute(
									new RegExp( '!/companies/stores/stocks!' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/abc',
				null,
			],
			#add matching Route after a non matching Route doesn't match
			[
				new WriteRouteGroup(
					new RegExp( '!/companies!' ),
					[
						new WriteRouteGroup(
							new RegExp( '!/companies/stores!' ),
							[
								new WriteRoute(
									new RegExp( '!/companies/stores/(?<storeId>\d*)$!' ),
									new ValidPostRequestHandler()
								),
								new WriteRoute(
									new RegExp( '!/companies/stocks$!' ),
									new IceHawkWriteRequestHandler()
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
		$expectedRequestHandler = new PatchRequestHandler();

		$companyGroup = new WriteRouteGroup( new RegExp( '!/companies!' ) );
		$storesGroup  = new WriteRouteGroup( new RegExp( '!/companies/stores!' ) );
		$storeGroup   = new WriteRouteGroup( new RegExp( '!/companies/stores/store!' ) );
		$stocksGroup  = new WriteRouteGroup( new RegExp( '!/companies/stores/store/stocks!' ) );

		$companyRoute  = new WriteRoute( new RegExp( '!/companies$!' ), new PostRequestHandler() );
		$membersRoute  = new WriteRoute( new RegExp( '!/companies/members$!' ), new IceHawkWriteRequestHandler() );
		$storesRoute   = new WriteRoute( new RegExp( '!/companies/stores$!' ), new PutRequestHandler() );
		$storeRoute    = new WriteRoute( new RegExp( '!/companies/stores/store$!' ), new DeleteRequestHandler() );
		$stocksRoute   =
			new WriteRoute( new RegExp( '!/companies/stores/store/stocks$!' ), new ValidPostRequestHandler() );
		$testRoute     = new WriteRoute( new RegExp( '!/companies/stores/store/test$!' ), $expectedRequestHandler );
		$productsRoute = new WriteRoute(
			new RegExp( '!/companies/stores/store/products$!' ), new BodyDataRequestHandler()
		);
		$stockRoute    = new WriteRoute(
			new RegExp( '!/companies/stores/store/stocks/stock$!' ), new AnotherPostRequestHandler()
		);

		$stocksGroup->addRoute( $stocksRoute )->addRoute( $stockRoute );
		$storeGroup->addRoute( $storeRoute )->addRoute( $stocksGroup )->addRoute( $testRoute )->addRoute(
			$productsRoute
		);
		$storesGroup->addRoute( $storeGroup )->addRoute( $stocksGroup )->addRoute( $storesRoute );

		$companyGroup->addRoute( $membersRoute )->addRoute( $companyRoute )->addRoute( $storesGroup );

		$result = $companyGroup->matches( '/companies/stores/store/test' );

		$this->assertEquals( $expectedRequestHandler, $companyGroup->getRequestHandler() );
		$this->assertTrue( $result );
	}
}
