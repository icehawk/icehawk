<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Routing\WriteRouteGroup;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\BodyDataRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PatchRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\AnotherPostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\ValidPostRequestHandler;

class WriteRouteGroupTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider routeProvider
	 */
	public function testFindRouteForRequest( WriteRouteGroup $groupedRoute, string $uri, $expectedRequestHandler )
	{
		$requestInfo = new RequestInfo( [
			'REQUEST_METHOD' => 'POST',
			'REQUEST_URI'    => $uri,
		]);

		$groupedRoute->matches( $requestInfo );

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

		$requestInfo = new RequestInfo( 
			[ 'REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/companies/stores/store/test' ] 
		);
		$result = $companyGroup->matches( $requestInfo );

		$this->assertEquals( $expectedRequestHandler, $companyGroup->getRequestHandler() );
		$this->assertTrue( $result );
	}
}