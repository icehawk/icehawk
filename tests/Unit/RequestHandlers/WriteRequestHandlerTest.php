<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use IceHawk\IceHawk\PubSub\EventPublisher;
use IceHawk\IceHawk\RequestHandlers\WriteRequestHandler;
use IceHawk\IceHawk\Requests\WriteRequest;
use IceHawk\IceHawk\Requests\WriteRequestInput;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\Patterns\RegExp;
use IceHawk\IceHawk\Routing\WriteRoute;
use IceHawk\IceHawk\Tests\Unit\Mocks\PhpStreamMock;

class WriteRequestHandlerTest extends \PHPUnit_Framework_TestCase
{
	public function parameterProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit', 'tested',
				[ 'unit' => 'tested', 'test' => 'unit' ],
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test', 'units',
				[ 'unit' => 'test', 'test' => 'units' ],
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit', 'units',
				[ 'unit' => 'units' ],
			],
		];
	}

	/**
	 * @dataProvider parameterProvider
	 * @runInSeparateProcess
	 */
	public function testUriParamsOverwritesPostParams(
		array $postData, string $uriKey, string $uriValue, array $expectedInputParams
	)
	{
		$_POST = $postData;

		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => sprintf( '/domain/test_request_param/%s/%s', $uriKey, $uriValue ),
			]
		);

		$expectedWriteRequest = new WriteRequest(
			$requestInfo, new WriteRequestInput( '', $expectedInputParams )
		);

		$requestHandler = $this->getMockBuilder( HandlesPostRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' )->with( $this->equalTo( $expectedWriteRequest ) );

		$regExp     = new RegExp(
			sprintf( '#^/domain/test_request_param/%s/(%s)$#', $uriKey, $uriValue ), [ $uriKey ]
		);
		$writeRoute = new WriteRoute( $regExp, $requestHandler );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ $writeRoute ] );

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCanGetBodyDataFromInputStream()
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', 'body data' );

		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/domain/test_body_data' ] );

		$expectedWriteRequest = new WriteRequest(
			$requestInfo, new WriteRequestInput( 'body data', [] )
		);

		$requestHandler = $this->getMockBuilder( HandlesPostRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' )->with( $this->equalTo( $expectedWriteRequest ) );

		$writeRoute = new WriteRoute( new Literal( '/domain/test_body_data' ), $requestHandler );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ $writeRoute ] );

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		stream_wrapper_restore( "php" );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testMissingWriteRoutesHandledByFinaleWriteResponder()
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/test' ] );

		$finalWriteResponder = $this->getMockBuilder( RespondsFinallyToWriteRequest::class )->getMockForAbstractClass();
		$finalWriteResponder->method( 'handleUncaughtException' )
		                    ->will(
			                    $this->returnCallback(
				                    function ()
				                    {
					                    echo 'fine';
				                    }
			                    )
		                    );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ ] );
		$config->method( 'getFinalWriteResponder' )->willReturn( $finalWriteResponder );

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		$this->expectOutputString( 'fine' );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testExceptionHandledByFinaleWriteResponder()
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/test' ] );
		$exception   = new \Exception();

		$finalWriteResponder = $this->getMockBuilder( RespondsFinallyToWriteRequest::class )->getMockForAbstractClass();
		$finalWriteResponder->method( 'handleUncaughtException' )
		                    ->will(
			                    $this->returnCallback(
				                    function ( $exception )
				                    {
					                    echo get_class( $exception );
				                    }
			                    )
		                    );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->method( 'getFinalWriteResponder' )->willReturn( $finalWriteResponder );

		$requestHandler = $this->getMockBuilder( HandlesPostRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )
		               ->method( 'handle' )
		               ->will( $this->throwException( $exception ) );

		$writeRoute = new WriteRoute( new Literal( '/test' ), $requestHandler );

		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ $writeRoute ] );

		$writeRequestHandler = new WriteRequestHandler( $config, new EventPublisher() );
		$writeRequestHandler->handleRequest();

		$this->expectOutputString( get_class( $exception ) );
	}
}
