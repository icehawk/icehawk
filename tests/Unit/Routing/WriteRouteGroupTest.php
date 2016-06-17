<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Routing\Patterns\ExactRegExp;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Routing\WriteRouteGroup;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\IceHawkWriteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
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

		$this->assertSame( $expectedMatched, $matched );
		$this->assertEquals( $expectedRequestHandler, $groupedRoute->getRequestHandler() );
	}

	public function routeProvider()
	{
		return [
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
									new ExactRegExp( '/companies/stores/stocks/(?<storeId>\d*)' ),
									new ValidWriteTestRequestHandler()
								),
								new WriteRoute(
									new ExactRegExp( '/companies/stores/stocks$' ),
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
		];
	}
}