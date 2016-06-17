<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Routing\Patterns\ExactRegExp;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Routing\WriteRouteGroup;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\BodyDataRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PatchRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\RequestParamsRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\ValidWriteTestRequestHandler;

class WriteRouteGroupTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest(
		WriteRouteGroup $groupedRoute, string $uri, bool $expectedMatched, HandlesWriteRequest $expectedRequestHandler
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
				new WriteRouteGroup(
					new ExactRegExp( '/companies' ),
					new PostRequestHandler(),
					[
						new WriteRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new PutRequestHandler(),
							[
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRoute(
									new ExactRegExp( '/companies/stores/stocks' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/123',
				true,
				new ValidWriteTestRequestHandler(),
			],
			#Route through part-matching routes to finally find the exactly matching route (last of last route-list)
			[
				new WriteRouteGroup(
					new ExactRegExp( '/companies' ),
					new PostRequestHandler(),
					[
						new WriteRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new PutRequestHandler(),
							[
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/stocks' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks',
				true,
				new IceHawkWriteRequestHandler(),
			],
			[
				new WriteRouteGroup(
					new ExactRegExp( '/companies' ),
					new PostRequestHandler(),
					[
						new WriteRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new PutRequestHandler(),
							[
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRoute(
									new ExactRegExp( '/companies/stores/stock$' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies',
				true,
				new PostRequestHandler(),
			],
			#Use only WriteRouteGroups to route through part-matching routes to a non existing route
			[
				new WriteRouteGroup(
					new ExactRegExp( '/companies' ),
					new PostRequestHandler(),
					[
						new WriteRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new PutRequestHandler(),
							[
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/stocks/(?<stockId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/stocks' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/stocks/doesnotexist',
				false,
				new PostRequestHandler(),
			],
			#Use Regex to finish on first route with negative match result
			[
				new WriteRouteGroup(
					new ExactRegExp( '/companies$' ),
					new PostRequestHandler(),
					[
						new WriteRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new PutRequestHandler(),
							[
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRoute(
									new ExactRegExp( '/companies/stores/stocks' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores',
				false,
				new PostRequestHandler(),
			],
			#use RegExp within a route
			[
				new WriteRouteGroup(
					new ExactRegExp( '/companies' ),
					new PostRequestHandler(),
					[
						new WriteRoute( new RegExp( '!^/companies/stores!' ), new DeleteRequestHandler() ),
						new WriteRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new PutRequestHandler(),
							[
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRoute(
									new ExactRegExp( '/companies/stores/stocks' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stores/abc',
				true,
				new DeleteRequestHandler(),
			],
			#add matching Route after a non matching Route doesn't match
			[
				new WriteRouteGroup(
					new ExactRegExp( '/companies' ),
					new PostRequestHandler(),
					[
						new WriteRouteGroup(
							new ExactRegExp( '/companies/stores' ),
							new PutRequestHandler(),
							[
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stores/(?<storeId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRouteGroup(
									new ExactRegExp( '/companies/stocks' ),
									new IceHawkWriteRequestHandler()
								),
							]
						),
					]
				),
				'/companies/stocks',
				false,
				new PostRequestHandler(),
			],
		];
	}

	public function testAddingRoutes()
	{
		$expectedRequestHandler = new PatchRequestHandler();

		$companyRoute  = new WriteRouteGroup( new ExactRegExp( '/companies' ), new PostRequestHandler() );
		$membersRoute  = new WriteRouteGroup( new ExactRegExp( '/companies/members' ), new IceHawkWriteRequestHandler() );
		$storesRoute   = new WriteRouteGroup( new ExactRegExp( '/companies/stores' ), new PutRequestHandler() );
		$storeRoute    = new WriteRouteGroup( new ExactRegExp( '/companies/stores/store' ), new DeleteRequestHandler() );
		$stocksRoute   = new WriteRouteGroup( new ExactRegExp( '/companies/stores/store/stocks' ), new ValidWriteTestRequestHandler() );
		$testRoute     = new WriteRouteGroup( new ExactRegExp( '/companies/stores/store/test' ), $expectedRequestHandler );
		$productsRoute = new WriteRouteGroup( new ExactRegExp( '/companies/stores/store/products' ), new BodyDataRequestHandler() );
		$stockRoute    = new WriteRouteGroup( new ExactRegExp( '/companies/stores/store/stocks/stock' ), new RequestParamsRequestHandler() );

		$stocksRoute->addRoute( $stockRoute );
		$storeRoute->addRoute( $stocksRoute )->addRoute( $productsRoute )->addRoute( $testRoute );
		$storesRoute->addRoute( $storeRoute );
		$companyRoute->addRoute( $membersRoute )->addRoute( $storesRoute );

		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => '/companies/stores/store/test' ] );

		$result = $companyRoute->matches( $requestInfo );
		
		$this->assertTrue( $result );
		$this->assertEquals( $expectedRequestHandler , $companyRoute->getRequestHandler() );
	}
}