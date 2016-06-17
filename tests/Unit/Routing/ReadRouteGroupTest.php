<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Routing\Patterns\ExactRegExp;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Routing\ReadRouteGroup;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\IceHawkReadRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\RequestParamsRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\ValidReadTestRequestHandler;

class ReadRouteGroupTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest(
		ReadRouteGroup $groupedRoute, string $uri, bool $expectedMatched, HandlesReadRequest $expectedRequestHandler
	)
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => $uri ] );
		$matched     = $groupedRoute->matches( $requestInfo );

		$this->assertEquals( $expectedRequestHandler, $groupedRoute->getRequestHandler() );
		$this->assertSame( $expectedMatched, $matched );
	}

	public function routeProvider()
	{
		return [
			[
				#Route through part-matching routes to finally find the exactly matching route (first of last route-list)
				new ReadRouteGroup(
					new ExactRegExp( '/companies' ),
					new GetRequestHandler(),
					[
						new ReadRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new HeadRequestHandler(),
							[
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new IceHawkReadRequestHandler()
								),
								new ReadRoute(
									new ExactRegExp( '/companies/stores/stocks' ),
									new ValidReadTestRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/123',
				true,
				new IceHawkReadRequestHandler(),
			],
			#Route through part-matching routes to finally find the exactly matching route (last of last route-list)
			[
				new ReadRouteGroup(
					new ExactRegExp( '/companies' ),
					new GetRequestHandler(),
					[
						new ReadRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new IceHawkReadRequestHandler(),
							[
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidReadTestRequestHandler()
								),
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/stocks' ),
									new RequestParamsRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks',
				true,
				new RequestParamsRequestHandler(),
			],
			[
				new ReadRouteGroup(
					new ExactRegExp( '/companies' ),
					new GetRequestHandler(),
					[
						new ReadRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new IceHawkReadRequestHandler(),
							[
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidReadTestRequestHandler()
								),
								new ReadRoute(
									new ExactRegExp( '/companies/stores/stock$' ),
									new RequestParamsRequestHandler()
								),
							]
						),
					]
				),
				'/companies',
				true,
				new GetRequestHandler(),
			],
			#Use only ReadRouteGroups to route through part-matching routes to a non existing route
			[
				new ReadRouteGroup(
					new ExactRegExp( '/companies' ),
					new GetRequestHandler(),
					[
						new ReadRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new IceHawkReadRequestHandler(),
							[
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/stocks/(?<stockId>\d*)' ),
									new ValidReadTestRequestHandler()
								),
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/stocks' ),
									new RequestParamsRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks/doesnotexist',
				false,
				new GetRequestHandler(),
			],
			#Use Regex to finish on first route with negative match result
			[
				new ReadRouteGroup(
					new ExactRegExp( '/companies$' ),
					new GetRequestHandler(),
					[
						new ReadRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new IceHawkReadRequestHandler(),
							[
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidReadTestRequestHandler()
								),
								new ReadRoute(
									new ExactRegExp( '/companies/stores/stocks' ),
									new RequestParamsRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores',
				false,
				new GetRequestHandler(),
			],
			#use RegExp within a route
			[
				new ReadRouteGroup(
					new ExactRegExp( '/companies' ),
					new GetRequestHandler(),
					[
						new ReadRoute( new RegExp( '!^/companies/stores!' ), new IceHawkReadRequestHandler() ),
						new ReadRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new ValidReadTestRequestHandler(),
							[
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new HeadRequestHandler()
								),
								new ReadRoute(
									new ExactRegExp( '/companies/stores/stocks' ),
									new RequestParamsRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/abc',
				true,
				new IceHawkReadRequestHandler(),
			],
			#add matching Route after a non matching Route doesn't match
			[
				new ReadRouteGroup(
					new ExactRegExp( '/companies' ),
					new GetRequestHandler(),
					[
						new ReadRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new IceHawkReadRequestHandler(),
							[
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidReadTestRequestHandler()
								),
								new ReadRouteGroup(
									new ExactRegExp( '/companies/stocks' ),
									new RequestParamsRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stocks',
				false,
				new GetRequestHandler(),
			],
		];
	}

	public function testAddingRoutes()
	{
		$expectedRequestHandler = new RequestParamsRequestHandler();

		$companyRoute = new ReadRouteGroup( new ExactRegExp( '/companies' ), new GetRequestHandler() );
		$membersRoute = new ReadRouteGroup( new ExactRegExp( '/companies/members' ), new IceHawkReadRequestHandler() );
		$storesRoute  = new ReadRouteGroup( new ExactRegExp( '/companies/stores' ), new ValidReadTestRequestHandler() );
		$storeRoute   = new ReadRouteGroup( new ExactRegExp( '/companies/stores/store' ), new HeadRequestHandler() );
		$stocksRoute  = new ReadRouteGroup(
			new ExactRegExp( '/companies/stores/stocks' ), new RequestParamsRequestHandler()
		);

		$storesRoute->addRoute( $storeRoute )->addRoute( $stocksRoute );
		$companyRoute->addRoute( $membersRoute )->addRoute( $storesRoute );

		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => '/companies/stores/stocks' ] );

		$result = $companyRoute->matches( $requestInfo );

		$this->assertTrue( $result );
		$this->assertEquals( $expectedRequestHandler, $companyRoute->getRequestHandler() );
	}
}