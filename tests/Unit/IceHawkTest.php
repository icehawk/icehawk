<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Defaults\FinalReadResponder;
use Fortuneglobe\IceHawk\Defaults\FinalWriteResponder;
use Fortuneglobe\IceHawk\Defaults\IceHawkConfig;
use Fortuneglobe\IceHawk\Defaults\IceHawkDelegate;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\HandlingReadRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\InitializingIceHawkEvent;
use Fortuneglobe\IceHawk\Events\ReadRequestWasHandledEvent;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\IceHawk;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;
use Fortuneglobe\IceHawk\Routing\Patterns\Literal;
use Fortuneglobe\IceHawk\Routing\ReadRoute;
use Fortuneglobe\IceHawk\Routing\WriteRoute;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\GetRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Read\HeadRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\DeleteRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PostRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\Domain\Write\PutRequestHandler;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestFinalReadRequestResponder;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestFinalWriteRequestResponder;

class IceHawkTest extends \PHPUnit_Framework_TestCase
{
	public function testDelegateMethodsWillBeCalledDuringInitialization()
	{
		$config   = new IceHawkConfig();
		$delegate = $this->prophesize( SetsUpEnvironment::class );

		$requestInfo       = RequestInfo::fromEnv();

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

		$eventListener     = $this->getMockBuilder( SubscribesToEvents::class )
		                          ->setMethods( [ 'acceptsEvent', 'notify' ] )
		                          ->getMockForAbstractClass();
		
		$eventListener->expects( $this->at(0) )
		              ->method( 'acceptsEvent' )
		              ->with( $this->equalTo( $initializingEvent ) )
		              ->willReturn( true );

		$eventListener->expects( $this->at(1) )
		              ->method( 'notify' )
		              ->with( $this->equalTo( $initializingEvent ) );

		$eventListener->expects( $this->at(2) )
		              ->method( 'acceptsEvent' )
		              ->with( $this->equalTo( $initializedEvent ) )
		              ->willReturn( true );

		$eventListener->expects( $this->at(3) )
		              ->method( 'notify' )
		              ->with( $this->equalTo( $initializedEvent ) );
		
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )
		       ->method( 'getEventSubscribers' )
		       ->willReturn( [ $eventListener ] );

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
				'REQUEST_URI'    => '/domain/ice_hawk_read',
			]
		);

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getFinalReadRequestResponder' )->willReturn(
			new FinalReadResponder()
		);
		$config->expects( $this->once() )->method( 'getFinalWriteRequestResponder' )->willReturn(
			new FinalWriteResponder()
		);
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'Handler method for get request called.' );
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
				'REQUEST_URI'    => '/domain/ice_hawk_write',
			]
		);

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getFinalReadRequestResponder' )->willReturn(
			new FinalReadResponder()
		);
		$config->expects( $this->once() )->method( 'getFinalWriteRequestResponder' )->willReturn(
			new FinalWriteResponder()
		);
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'Handler method for post request called.' );
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
				[ 'POST' ],
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
	public function testPublishesEventsWhenHandlingRequest()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/valid_read_test',
			]
		);

		$initEvent     = new IceHawkWasInitializedEvent( $requestInfo );
		$getRequest    = new ReadRequest( $requestInfo, new ReadRequestInput( [ ] ) );
		$handlingEvent = new HandlingReadRequestEvent( $getRequest );
		$handledEvent  = new ReadRequestWasHandledEvent( $getRequest );

		$eventListener = $this->getMockBuilder( SubscribesToEvents::class )
		                      ->setMethods( [ 'acceptsEvent', 'notify' ] )
		                      ->getMockForAbstractClass();

		$eventListener->expects( $this->exactly( 3 ) )
		              ->method( 'acceptsEvent' )
		              ->withConsecutive(
			              [ $this->equalTo( $initEvent ) ],
			              [ $this->equalTo( $handlingEvent ) ],
			              [ $this->equalTo( $handledEvent ) ]
		              )
		              ->willReturn( true );

		$eventListener->expects( $this->exactly( 3 ) )
		              ->method( 'notify' )
		              ->withConsecutive(
			              [ $this->equalTo( $initEvent ) ],
			              [ $this->equalTo( $handlingEvent ) ],
			              [ $this->equalTo( $handledEvent ) ]
		              );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getFinalReadRequestResponder' )->willReturn(
			new FinalReadResponder()
		);
		$config->expects( $this->once() )->method( 'getFinalWriteRequestResponder' )->willReturn(
			new FinalWriteResponder()
		);
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [ $eventListener ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();
	}

	public function requestHandlerProvider()
	{
		return [
			[ '/domain/unresolvable_write_uri', 'POST' ],
			[ '/domain/unresolvable_write_uri', 'PUT' ],
			[ '/domain/unresolvable_write_uri', 'PATCH' ],
			[ '/domain/unresolvable_write_uri', 'DELETE' ],
			[ '/domain/unresolvable_read_uri', 'GET' ],
			[ '/domain/unresolvable_read_uri', 'HEAD' ],
		];
	}

	/**
	 * @dataProvider requestHandlerProvider
	 * @runInSeparateProcess
	 */
	public function testFinalResponseRespondsIfUriCouldNotBeResolved( string $uri, string $requestMethod )
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => $requestMethod,
				'REQUEST_URI'    => $uri,
			]
		);

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getReadRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getWriteRoutes' )->willReturn( [ ] );
		$config->expects( $this->once() )->method( 'getFinalReadRequestResponder' )->willReturn(
			new TestFinalReadRequestResponder()
		);
		$config->expects( $this->once() )->method( 'getFinalWriteRequestResponder' )->willReturn(
			new TestFinalWriteRequestResponder()
		);
		$config->expects( $this->once() )->method( 'getEventSubscribers' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( UnresolvedRequest::class );
	}
}