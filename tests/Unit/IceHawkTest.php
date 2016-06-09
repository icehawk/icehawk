<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Defaults\FinalReadResponder;
use Fortuneglobe\IceHawk\Defaults\FinalWriteResponder;
use Fortuneglobe\IceHawk\Defaults\IceHawkConfig;
use Fortuneglobe\IceHawk\Defaults\IceHawkDelegate;
use Fortuneglobe\IceHawk\Defaults\ReadRequestResolver;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Defaults\UriRewriter;
use Fortuneglobe\IceHawk\Defaults\WriteRequestResolver;
use Fortuneglobe\IceHawk\Events\HandlingReadRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\ReadRequestWasHandledEvent;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\IceHawk;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\HandlesDeleteRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesHeadRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesPatchRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesPutRequest;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;
use Fortuneglobe\IceHawk\Responses\MethodNotAllowed;
use Fortuneglobe\IceHawk\Responses\Redirect;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestFinalReadRequestResponder;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestFinalWriteRequestResponder;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestReadRequestResolver;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestWriteRequestResolver;

class IceHawkTest extends \PHPUnit_Framework_TestCase
{
	public function testDelegateMethodsWillBeCalledDuringInitialization()
	{
		$config   = new IceHawkConfig();
		$delegate = $this->prophesize( SetsUpEnvironment::class );

		$delegate->setUpErrorHandling()->shouldBeCalled();
		$delegate->setUpSessionHandling()->shouldBeCalled();
		$delegate->setUpGlobalVars()->shouldBeCalled();

		$iceHawk = new IceHawk( $config, $delegate->reveal() );
		$iceHawk->init();
	}

	public function testPublishesEventWhenInitializationIsDone()
	{
		$requestInfo   = RequestInfo::fromEnv();
		$initEvent     = new IceHawkWasInitializedEvent( $requestInfo );
		$eventListener = $this->getMockBuilder( SubscribesToEvents::class )
		                      ->setMethods( [ 'acceptsEvent', 'notify' ] )
		                      ->getMockForAbstractClass();

		$eventListener->expects( $this->once() )
		              ->method( 'acceptsEvent' )
		              ->with( $this->equalTo( $initEvent ) )
		              ->willReturn( true );

		$eventListener->expects( $this->once() )
		              ->method( 'notify' )
		              ->with( $this->equalTo( $initEvent ) );

		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new WriteRequestResolver()
		);
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( RequestInfo::fromEnv() );
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
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn(
			new TestReadRequestResolver()
		);
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new WriteRequestResolver()
		);
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
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new TestWriteRequestResolver()
		);
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

	public function optionRequestUriAndMethodProvider()
	{
		return [
			[ '/domain/post', 'POST' ],
			[ '/domain/get', 'GET' ],
			[ '/domain/put', 'PUT' ],
			[ '/domain/head', 'HEAD' ],
			[ '/domain/delete', 'DELETE' ],
			[ '/domain/patch', 'PATCH' ],
		];
	}

	/**
	 * @dataProvider optionRequestUriAndMethodProvider
	 * @runInSeparateProcess
	 */
	public function testCanCallHandlerForOptionRequest( string $uri, string $requestMethod )
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'OPTIONS',
				'REQUEST_URI'    => $uri,
			]
		);

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn(
			new TestReadRequestResolver()
		);
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new TestWriteRequestResolver()
		);
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

		$this->assertContains( sprintf( 'Allow: %s', $requestMethod ), xdebug_get_headers() );
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testCanRewriteUrl()
	{
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();

		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/ice_hawk_rewrite',
			]
		);

		$redirect = new Redirect( '/domain/rewritten', HttpCode::MOVED_PERMANENTLY );

		$uriRewriter = $this->getMockBuilder( RewritesUri::class )->setMethods( [ 'rewrite' ] )->getMock();
		$uriRewriter->expects( $this->once() )->method( 'rewrite' )->with( $requestInfo )->willReturn( $redirect );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( $uriRewriter );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new WriteRequestResolver()
		);
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

		$reflection = new \ReflectionClass( '\\Fortuneglobe\\IceHawk\\Responses\\Redirect' );
		$getBody    = $reflection->getMethod( 'getBody' );
		$getBody->setAccessible( true );
		$getContentType = $reflection->getMethod( 'getContentType' );
		$getContentType->setAccessible( true );

		$expectedBody = $getBody->invoke( $redirect );
		$contentType  = $getContentType->invoke( $redirect );

		$this->expectOutputString( $expectedBody );
		$this->assertContains( 'Location: /domain/rewritten', xdebug_get_headers() );
		$this->assertEquals( HttpCode::MOVED_PERMANENTLY, http_response_code() );
		$this->assertEquals( 'text/html', $contentType );
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
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn(
			new TestReadRequestResolver()
		);
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new WriteRequestResolver()
		);
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

	public function writeRequestHandlerProvider()
	{
		return [
			[ HandlesPostRequest::class, '/domain/post', 'PUT' ],
			[ HandlesPostRequest::class, '/domain/post', 'DELETE' ],
			[ HandlesPostRequest::class, '/domain/post', 'PATCH' ],
			[ HandlesPutRequest::class, '/domain/put', 'POST' ],
			[ HandlesPutRequest::class, '/domain/put', 'DELETE' ],
			[ HandlesPutRequest::class, '/domain/put', 'POST' ],
			[ HandlesDeleteRequest::class, '/domain/delete', 'POST' ],
			[ HandlesDeleteRequest::class, '/domain/delete', 'PUT' ],
			[ HandlesDeleteRequest::class, '/domain/delete', 'PATCH' ],
			[ HandlesPatchRequest::class, '/domain/patch', 'POST' ],
			[ HandlesPatchRequest::class, '/domain/patch', 'PUT' ],
			[ HandlesPatchRequest::class, '/domain/patch', 'DELETE' ],
		];
	}

	/**
	 * @dataProvider writeRequestHandlerProvider
	 * @runInSeparateProcess
	 */
	public function testInvalidWriteMethodRespondsWithMethodIsNotAllowed(
		string $expectedInterface, string $uri, string $wrongWriteRequestMethod
	)
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => $wrongWriteRequestMethod,
				'REQUEST_URI'    => $uri,
			]
		);

		$resolver           = new TestWriteRequestResolver();
		$writeHandlerRouter = $resolver->resolve( $requestInfo );
		$writeHandler       = $writeHandlerRouter->getRequestHandler();

		$this->assertInstanceOf( $expectedInterface, $writeHandler );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn( $resolver );
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

		$methodNotAllowedResponse = new MethodNotAllowed( $wrongWriteRequestMethod );

		$reflection = new \ReflectionClass( '\\Fortuneglobe\\IceHawk\\Responses\\MethodNotAllowed' );
		$getBody    = $reflection->getMethod( 'getBody' );
		$getBody->setAccessible( true );

		$expectedBody = $getBody->invoke( $methodNotAllowedResponse );

		$this->expectOutputString( $expectedBody );
		$this->assertEquals( HttpCode::METHOD_NOT_ALLOWED, http_response_code() );
	}

	public function readRequestHandlerProvider()
	{
		return [
			[ HandlesGetRequest::class, '/domain/get', 'HEAD' ],
			[ HandlesHeadRequest::class, '/domain/head', 'GET' ],
		];
	}

	/**
	 * @dataProvider readRequestHandlerProvider
	 * @runInSeparateProcess
	 */
	public function testInvalidReadMethodRespondsWithMethodIsNotAllowed(
		string $expectedInterface, string $uri, string $wrongReadRequestMethod
	)
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => $wrongReadRequestMethod,
				'REQUEST_URI'    => $uri,
			]
		);

		$resolver          = new TestReadRequestResolver();
		$readHandlerRouter = $resolver->resolve( $requestInfo );
		$readHandler       = $readHandlerRouter->getRequestHandler();

		$this->assertInstanceOf( $expectedInterface, $readHandler );

		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn( $resolver );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new WriteRequestResolver()
		);
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

		$methodNotAllowedResponse = new MethodNotAllowed( $wrongReadRequestMethod );

		$reflection = new \ReflectionClass( '\\Fortuneglobe\\IceHawk\\Responses\\MethodNotAllowed' );
		$getBody    = $reflection->getMethod( 'getBody' );
		$getBody->setAccessible( true );

		$expectedBody = $getBody->invoke( $methodNotAllowedResponse );

		$this->expectOutputString( $expectedBody );
		$this->assertEquals( HttpCode::METHOD_NOT_ALLOWED, http_response_code() );
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
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadRequestResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getWriteRequestResolver' )->willReturn(
			new WriteRequestResolver()
		);
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