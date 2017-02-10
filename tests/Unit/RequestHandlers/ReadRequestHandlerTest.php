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
use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use IceHawk\IceHawk\PubSub\EventPublisher;
use IceHawk\IceHawk\RequestHandlers\ReadRequestHandler;
use IceHawk\IceHawk\Requests\ReadRequest;
use IceHawk\IceHawk\Requests\ReadRequestInput;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\Patterns\RegExp;
use IceHawk\IceHawk\Routing\ReadRoute;

class ReadRequestHandlerTest extends \PHPUnit\Framework\TestCase
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
	 * @param array  $getData
	 * @param string $uriKey
	 * @param string $uriValue
	 * @param array  $expectedParams
	 */
	public function testUriParamsOverwritesGetParams(
		array $getData, string $uriKey, string $uriValue, array $expectedParams
	)
	{
		$_GET       = $getData;
		$requestUri = sprintf( '/domain/test_request_param/%s/%s', $uriKey, $uriValue );

		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => $requestUri ] );
		$cookies         = new Cookies( [] );
		$expectedRequest = new ReadRequest(
			$requestInfo,
			$cookies,
			new ReadRequestInput( $expectedParams )
		);

		$requestHandler = $this->getMockBuilder( HandlesGetRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' )->with( $this->equalTo( $expectedRequest ) );

		$regExp = new RegExp( sprintf( '#^/domain/test_request_param/%s/(%s)$#', $uriKey, $uriValue ), [ $uriKey ] );
		$readRoute = new ReadRoute( $regExp, $requestHandler );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getCookies' )->willReturn( $cookies );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [ $readRoute ] );

		$readRequestHandler = new ReadRequestHandler( $requestInfo, $config, new EventPublisher() );
		$readRequestHandler->handleRequest();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testMissingReadRoutesHandledByFinaleReadResponder()
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/test' ] );

		$finalReadResponder = $this->getMockBuilder( RespondsFinallyToReadRequest::class )->getMockForAbstractClass();
		$finalReadResponder->method( 'handleUncaughtException' )
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
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [] );
		$config->method( 'getFinalReadResponder' )->willReturn( $finalReadResponder );

		$readRequestHandler = new ReadRequestHandler( $requestInfo, $config, new EventPublisher() );
		$readRequestHandler->handleRequest();

		$this->expectOutputString( 'fine' );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testExceptionHandledByFinaleReadResponder()
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/test' ] );

		$exception = new \Exception();

		$finalReadResponder = $this->getMockBuilder( RespondsFinallyToReadRequest::class )->getMockForAbstractClass();
		$finalReadResponder->method( 'handleUncaughtException' )
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
		$config->method( 'getFinalReadResponder' )->willReturn( $finalReadResponder );

		$requestHandler = $this->getMockBuilder( HandlesGetRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )
		               ->method( 'handle' )
		               ->will( $this->throwException( $exception ) );

		$readRoute = new ReadRoute( new Literal( '/test' ), $requestHandler );

		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [ $readRoute ] );

		$readRequestHandler = new ReadRequestHandler( $requestInfo, $config, new EventPublisher() );
		$readRequestHandler->handleRequest();

		$this->expectOutputString( get_class( $exception ) );
	}
}
