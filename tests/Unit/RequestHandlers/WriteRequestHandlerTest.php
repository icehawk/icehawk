<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\RequestHandlers;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\RequestHandlers\WriteRequestHandler;
use Fortuneglobe\IceHawk\Routing\Patterns\Literal;
use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\BodyDataRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\RequestParamsRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Mocks\PhpStreamMock;

class WriteRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	public function parameterProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit', 'tested',
				json_encode( [ 'unit' => 'tested', 'test' => 'unit' ] )
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test', 'units',
				json_encode( [ 'unit' => 'test', 'test' => 'units' ] )
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit', 'units',
				json_encode( [ 'unit' => 'units' ] )
			],
		];
	}

	/**
	 * @dataProvider parameterProvider
	 * @runInSeparateProcess
	 */
	public function testUriParamsOverwritesPostParams( array $postData, string $uriKey, string $uriValue, $expectedJson )
	{
		$_POST = $postData;

		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo( 
			[ 'REQUEST_METHOD' => 'POST',
		      'REQUEST_URI' => sprintf( '/domain/test_request_param/%s/%s', $uriKey, $uriValue ) ] 
		);

		$regExp     = new RegExp( 
			sprintf( '#^/domain/test_request_param/%s/(%s)$#', $uriKey, $uriValue ), [ $uriKey ] 
		);
		$writeRoute = new WriteRoute( $regExp, new RequestParamsRequestHandler() );

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ $writeRoute ] );

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		$this->expectOutputString( $expectedJson );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCanGetBodyDataFromInputStream( )
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', 'body data' );

		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/domain/test_body_data' ] );

		$writeRoute = new WriteRoute( new Literal( '/domain/test_body_data' ), new BodyDataRequestHandler() );
		
		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ $writeRoute ] );

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		$this->expectOutputString( 'body data' );

		stream_wrapper_restore( "php" );
	}
}
