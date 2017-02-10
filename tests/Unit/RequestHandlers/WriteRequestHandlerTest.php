<?php declare(strict_types = 1);
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

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Defaults\Cookies;
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

class WriteRequestHandlerTest extends \PHPUnit\Framework\TestCase
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
	 *
	 * @param array  $postData
	 * @param string $uriKey
	 * @param string $uriValue
	 * @param array  $expectedInputParams
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

		$cookies = new Cookies( [] );

		$expectedWriteRequest = new WriteRequest(
			$requestInfo,
			$cookies,
			new WriteRequestInput( '', $expectedInputParams )
		);

		$requestHandler = $this->getMockBuilder( HandlesPostRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' )->with( $this->equalTo( $expectedWriteRequest ) );

		$regExp     = new RegExp(
			sprintf( '#^/domain/test_request_param/%s/(%s)$#', $uriKey, $uriValue ), [ $uriKey ]
		);
		$writeRoute = new WriteRoute( $regExp, $requestHandler );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getCookies' )->willReturn( $cookies );
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
		$cookies     = new Cookies( [] );

		$expectedWriteRequest = new WriteRequest(
			$requestInfo,
			$cookies,
			new WriteRequestInput( 'body data', [] )
		);

		$requestHandler = $this->getMockBuilder( HandlesPostRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' )->with( $this->equalTo( $expectedWriteRequest ) );

		$writeRoute = new WriteRoute( new Literal( '/domain/test_body_data' ), $requestHandler );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getCookies' )->willReturn( $cookies );
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
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [] );
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
