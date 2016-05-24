<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\RequestHandlers;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\RequestHandlers\WriteRequestHandler;
use Fortuneglobe\IceHawk\RequestParsers\SimpleBodyParserFactory;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestInputWriteRequestResolver;
use Fortuneglobe\IceHawk\Tests\Unit\Mocks\PhpStreamMock;

class WriteRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	public function postAndBodyDataProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit=tested',
				json_encode( [ 'unit' => 'tested', 'test' => 'unit' ] )
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test=units',
				json_encode( [ 'unit' => 'test', 'test' => 'units' ] )
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit[test]=units',
				json_encode( [ 'unit' => [ 'test' => 'units' ] ] )
			],
		];
	}

	/**
	 * @dataProvider postAndBodyDataProvider
	 * @runInSeparateProcess
	 */
	public function testBodyParamsOverwritesPostParams( array $postData, string $body, $expectedJson )
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', $body );

		$_POST = $postData;

		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/domain/test_request_param' ] );

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new TestInputWriteRequestResolver()
		);
		$config->expects( $this->once() )->method( 'getBodyParserFactory' )->willReturn( new SimpleBodyParserFactory() );

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		$this->expectOutputString( $expectedJson );

		stream_wrapper_restore( "php" );
	}

	public function uriAndBodyDataProvider()
	{
		return [
			[
				'unit', 'test',
				'unit=tested',
				json_encode( [ 'unit' => 'test' ] )
			],
			[
				'test', 'unit',
				'test=units',
				json_encode( [ 'test' => 'unit' ] )
			],
		];
	}

	/**
	 * @dataProvider uriAndBodyDataProvider
	 * @runInSeparateProcess
	 */
	public function testUriParamsOverwritesBodyParams( string $uriKey, string $uriValue, string $body, $expectedJson )
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', $body );

		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo( 
			[ 'REQUEST_METHOD' => 'POST', 
			  'REQUEST_URI' => sprintf( '/domain/test_request_param/%s/%s', $uriKey, $uriValue ) ]
		);
		
		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new TestInputWriteRequestResolver()
		);
		$config->expects( $this->once() )->method( 'getBodyParserFactory' )->willReturn( new SimpleBodyParserFactory() );
		
		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		$this->expectOutputString( $expectedJson );

		stream_wrapper_restore( "php" );
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

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new TestInputWriteRequestResolver()
		);

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		$this->expectOutputString( 'body data' );

		stream_wrapper_restore( "php" );
	}
}
