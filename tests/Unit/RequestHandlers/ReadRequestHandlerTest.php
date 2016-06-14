<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\RequestHandlers;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\RequestHandlers\ReadRequestHandler;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\RequestParamsRequestHandler;

class ReadRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	public function parameterProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit', 'tested',
				json_encode( [ 'unit' => 'tested', 'test' => 'unit' ] ),
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test', 'units',
				json_encode( [ 'unit' => 'test', 'test' => 'units' ] ),
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit', 'units',
				json_encode( [ 'unit' => 'units' ] ),
			],
		];
	}

	/**
	 * @dataProvider parameterProvider
	 * @runInSeparateProcess
	 */
	public function testUriParamsOverwritesGetParams( array $getData, string $uriKey, string $uriValue, $expectedJson )
	{
		$_GET       = $getData;
		$requestUri = sprintf( '/domain/test_request_param/%s/%s', $uriKey, $uriValue );

		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => $requestUri ] );

		$regExp    = new RegExp( sprintf( '#^/domain/test_request_param/%s/(%s)$#', $uriKey, $uriValue ), [ $uriKey ] );
		$readRoute = new ReadRoute(	$regExp, new RequestParamsRequestHandler() );

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );

		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [ $readRoute ] );

		$readRequestHandler = new ReadRequestHandler( $config, new EventPublisher() );
		$readRequestHandler->handleRequest();

		$this->expectOutputString( $expectedJson );
	}
}
