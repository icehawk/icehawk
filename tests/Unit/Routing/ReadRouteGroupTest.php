<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
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
	public function testFindRouteForRequest( ReadRouteGroup $groupedRoute, string $uri, $expectedRequestHandler )
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => $uri ] );

		$groupedRoute->matches( $requestInfo );

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
									new ValidReadTestRequestHandler()
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
									new ValidReadTestRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks$#' ),
									new RequestParamsRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks',
				new RequestParamsRequestHandler(),
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
									new ValidReadTestRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stock$#' ),
									new RequestParamsRequestHandler()
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
									new ValidReadTestRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks$#' ),
									new RequestParamsRequestHandler()
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
									new ValidReadTestRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stores/stocks#' ),
									new RequestParamsRequestHandler()
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
									new ValidReadTestRequestHandler()
								),
								new ReadRoute(
									new RegExp( '#^/companies/stocks#' ),
									new RequestParamsRequestHandler()
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
		$expectedRequestHandler = new RequestParamsRequestHandler();

		$companyGroup = new ReadRouteGroup( new RegExp( '#^/companies#' ) );
		$storeGroup   = new ReadRouteGroup( new RegExp( '#^/companies/stores#' ) );

		$storeGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/stores$#' ), new ValidReadTestRequestHandler() )
		);

		$storeGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/stores/store#' ), new HeadRequestHandler() )
		);

		$storeGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/stores/stocks#' ), new RequestParamsRequestHandler() )
		);

		$companyGroup->addRoute( $storeGroup );

		$companyGroup->addRoute(
			new ReadRoute( new RegExp( '#^/companies/members#' ), new IceHawkReadRequestHandler() )
		);

		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => '/companies/stores/stocks' ] );

		$result = $companyGroup->matches( $requestInfo );

		$this->assertTrue( $result );
		$this->assertEquals( $expectedRequestHandler, $companyGroup->getRequestHandler() );
	}
}