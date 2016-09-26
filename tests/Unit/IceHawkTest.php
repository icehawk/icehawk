<?php
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

declare(strict_types = 1);
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

namespace IceHawk\IceHawk\Tests\Unit\Defaults;

use IceHawk\IceHawk\Constants\HttpCode;
use IceHawk\IceHawk\Defaults\IceHawkConfig;
use IceHawk\IceHawk\Defaults\IceHawkDelegate;
use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\HandlingReadRequestEvent;
use IceHawk\IceHawk\Events\HandlingWriteRequestEvent;
use IceHawk\IceHawk\Events\IceHawkWasInitializedEvent;
use IceHawk\IceHawk\Events\InitializingIceHawkEvent;
use IceHawk\IceHawk\Events\ReadRequestWasHandledEvent;
use IceHawk\IceHawk\Events\WriteRequestWasHandledEvent;
use IceHawk\IceHawk\IceHawk;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\Interfaces\HandlesGetRequest;
use IceHawk\IceHawk\Interfaces\HandlesPostRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use IceHawk\IceHawk\Interfaces\SetsUpEnvironment;
use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use IceHawk\IceHawk\Requests\ReadRequest;
use IceHawk\IceHawk\Requests\ReadRequestInput;
use IceHawk\IceHawk\Requests\WriteRequest;
use IceHawk\IceHawk\Requests\WriteRequestInput;
use IceHawk\IceHawk\Routing\Patterns\Literal;
use IceHawk\IceHawk\Routing\ReadRoute;
use IceHawk\IceHawk\Routing\WriteRoute;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use IceHawk\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;

class IceHawkTest extends \PHPUnit_Framework_TestCase
{
	public function testDelegateMethodsWillBeCalledDuringInitialization()
	{
		$config   = new IceHawkConfig();
		$delegate = $this->prophesize( SetsUpEnvironment::class );

		$requestInfo = RequestInfo::fromEnv();

		$delegate->setUpGlobalVars()->shouldBeCalled();
		$delegate->setUpErrorHandling( $requestInfo )->shouldBeCalled();
		$delegate->setUpSessionHandling( $requestInfo )->shouldBeCalled();

		$iceHawk = new IceHawk( $config, $delegate->reveal() );
		$iceHawk->init();
	}

	public function testPublishesEventWhenInitializationIsDone()
	{
		$requestInfo       = RequestInfo::fromEnv();
		$initializingEvent = new InitializingIceHawkEvent( $requestInfo );
		$initializedEvent  = new IceHawkWasInitializedEvent( $requestInfo );

		$eventListener = $this->getMockBuilder( SubscribesToEvents::class )
		                      ->setMethods( ['acceptsEvent', 'notify'] )
		                      ->getMockForAbstractClass();

		$eventListener->expects( $this->at( 0 ) )
		              ->method( 'acceptsEvent' )
		              ->with( $this->equalTo( $initializingEvent ) )
		              ->willReturn( true );

		$eventListener->expects( $this->at( 1 ) )
		              ->method( 'notify' )
		              ->with( $this->equalTo( $initializingEvent ) );

		$eventListener->expects( $this->at( 2 ) )
		              ->method( 'acceptsEvent' )
		              ->with( $this->equalTo( $initializedEvent ) )
		              ->willReturn( true );

		$eventListener->expects( $this->at( 3 ) )
		              ->method( 'notify' )
		              ->with( $this->equalTo( $initializedEvent ) );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )
		       ->method( 'getEventSubscribers' )
		       ->willReturn( [$eventListener] );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testHandlingMalformedRequestRespondsWithMethodNotImplemented()
	{
		$config   = new IceHawkConfig();
		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->assertContains( sprintf( 'Content-Type: %s; charset=%s', 'text/plain', 'utf-8' ), xdebug_get_headers() );
		$this->expectOutputString( sprintf( '%d - Method Not Implemented (%s)', HttpCode::NOT_IMPLEMENTED, '' ) );
		$this->assertEquals( HttpCode::NOT_IMPLEMENTED, http_response_code() );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCanCallHandlerForGetRequest()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/test',
			]
		);

		$requestHandler = $this->getMockBuilder( HandlesGetRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' )->will(
			$this->returnCallback(
				function ()
				{
					echo 'test';
				}
			)
		);

		$route = new ReadRoute( new Literal( '/test' ), $requestHandler );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [$route] );
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'test' );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCanCallHandlerForPostRequest()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/test',
			]
		);

		$requestHandler = $this->getMockBuilder( HandlesPostRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' )->will(
			$this->returnCallback(
				function ()
				{
					echo 'test';
				}
			)
		);

		$route = new WriteRoute( new Literal( '/test' ), $requestHandler );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [$route] );
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'test' );
	}

	public function RouteProvider()
	{
		return [
			[
				[
					new ReadRoute( new Literal( '/get/this' ), new GetRequestHandler() ),
					new ReadRoute( new Literal( '/get/that' ), new HeadRequestHandler() ),
					new ReadRoute( new Literal( '/get/this/again' ), new GetRequestHandler() ),
				],
				[
					new WriteRoute( new Literal( '/do/this' ), new PostRequestHandler() ),
					new WriteRoute( new Literal( '/do/this/again' ), new PostRequestHandler() ),
					new WriteRoute( new Literal( '/do/that' ), new PutRequestHandler() ),
					new WriteRoute( new Literal( '/do/whatever/you/want' ), new DeleteRequestHandler() ),
				],
				'/do/this',
				['POST'],
			],
		];
	}

	/**
	 * @dataProvider RouteProvider
	 * @runInSeparateProcess
	 */
	public function testCanCallHandlerForOptionRequest(
		array $readRoutes, array $writeRoutes, string $uri, array $expectedMethods
	)
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'OPTIONS',
				'REQUEST_URI'    => $uri,
			]
		);

		$config->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->method( 'getReadRoutes' )->willReturn( $readRoutes );
		$config->method( 'getWriteRoutes' )->willReturn( $writeRoutes );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->assertContains( sprintf( 'Allow: %s', implode( ',', $expectedMethods ) ), xdebug_get_headers() );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testPublishesEventsWhenHandlingReadRequest()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo( ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/test',] );

		$requestHandler = $this->getMockBuilder( HandlesGetRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' );

		$route = new ReadRoute( new Literal( '/test' ), $requestHandler );

		$initializingEvent = new InitializingIceHawkEvent( $requestInfo );
		$initEvent         = new IceHawkWasInitializedEvent( $requestInfo );
		$request           = new ReadRequest( $requestInfo, new ReadRequestInput( [] ) );
		$handlingEvent     = new HandlingReadRequestEvent( $request );
		$handledEvent      = new ReadRequestWasHandledEvent( $request );

		$eventListener = $this->getMockBuilder( SubscribesToEvents::class )
		                      ->setMethods( ['acceptsEvent', 'notify'] )
		                      ->getMockForAbstractClass();

		$eventListener->expects( $this->exactly( 4 ) )
		              ->method( 'acceptsEvent' )
		              ->withConsecutive(
			              [$this->equalTo( $initializingEvent )],
			              [$this->equalTo( $initEvent )],
			              [$this->equalTo( $handlingEvent )],
			              [$this->equalTo( $handledEvent )]
		              )
		              ->willReturn( true );

		$eventListener->expects( $this->exactly( 4 ) )
		              ->method( 'notify' )
		              ->withConsecutive(
			              [$this->equalTo( $initializingEvent )],
			              [$this->equalTo( $initEvent )],
			              [$this->equalTo( $handlingEvent )],
			              [$this->equalTo( $handledEvent )]
		              );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [$route] );
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [$eventListener] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testPublishesEventsWhenHandlingWriteRequest()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo( ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/test',] );

		$requestHandler = $this->getMockBuilder( HandlesPostRequest::class )->getMockForAbstractClass();
		$requestHandler->expects( $this->once() )->method( 'handle' );

		$route = new WriteRoute( new Literal( '/test' ), $requestHandler );

		$initializingEvent = new InitializingIceHawkEvent( $requestInfo );
		$initEvent         = new IceHawkWasInitializedEvent( $requestInfo );
		$request           = new WriteRequest( $requestInfo, new WriteRequestInput( '', [] ) );
		$handlingEvent     = new HandlingWriteRequestEvent( $request );
		$handledEvent      = new WriteRequestWasHandledEvent( $request );

		$eventListener = $this->getMockBuilder( SubscribesToEvents::class )
		                      ->setMethods( ['acceptsEvent', 'notify'] )
		                      ->getMockForAbstractClass();

		$eventListener->expects( $this->exactly( 4 ) )
		              ->method( 'acceptsEvent' )
		              ->withConsecutive(
			              [$this->equalTo( $initializingEvent )],
			              [$this->equalTo( $initEvent )],
			              [$this->equalTo( $handlingEvent )],
			              [$this->equalTo( $handledEvent )]
		              )
		              ->willReturn( true );

		$eventListener->expects( $this->exactly( 4 ) )
		              ->method( 'notify' )
		              ->withConsecutive(
			              [$this->equalTo( $initializingEvent )],
			              [$this->equalTo( $initEvent )],
			              [$this->equalTo( $handlingEvent )],
			              [$this->equalTo( $handledEvent )]
		              );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [$route] );
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [$eventListener] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testFinalReadResponderRespondsIfUriCouldNotBeResolved()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/test',
			]
		);

		$finalResponder = $this->getMockBuilder( RespondsFinallyToReadRequest::class )->getMockForAbstractClass();
		$finalResponder->expects( $this->once() )
		               ->method( 'handleUncaughtException' )
		               ->will(
			               $this->returnCallback(
				               function ()
				               {
					               echo 'test';
				               }
			               )
		               );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [] );
		$config->method( 'getFinalReadResponder' )->willReturn( $finalResponder );
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'test' );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testFinalWriteResponderRespondsIfUriCouldNotBeResolved()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/test',
			]
		);

		$finalResponder = $this->getMockBuilder( RespondsFinallyToWriteRequest::class )->getMockForAbstractClass();
		$finalResponder->expects( $this->once() )
		               ->method( 'handleUncaughtException' )
		               ->will(
			               $this->returnCallback(
				               function ()
				               {
					               echo 'test';
				               }
			               )
		               );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [] );
		$config->method( 'getFinalWriteResponder' )->willReturn( $finalResponder );
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'test' );
	}
}
