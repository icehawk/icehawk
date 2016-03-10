<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\IceHawk;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Defaults\IceHawkConfig;
use Fortuneglobe\IceHawk\Defaults\IceHawkDelegate;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Defaults\ReadRequestResolver;
use Fortuneglobe\IceHawk\Defaults\UriRewriter;
use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\RequestWasHandledEvent;
use Fortuneglobe\IceHawk\IceHawk;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Responses\Redirect;

class IceHawkTest extends \PHPUnit_Framework_TestCase
{
	public function testDelegateMethodsWillBeCalledDuringInitialization()
	{
		$config   = new IceHawkConfig();
		$delegate = $this->prophesize( SetsUpEnvironment::class );

		$delegate->setUpErrorHandling()->shouldBeCalled();
		$delegate->setUpSessionHandling()->shouldBeCalled();
		$delegate->setUpEnvironment()->shouldBeCalled();

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
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn( __NAMESPACE__ );
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( RequestInfo::fromEnv() );
		$config->expects( $this->once() )
		       ->method( 'getEventListeners' )
		       ->willReturn( [ $eventListener ] );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
	 */
	public function testHandlingMalformedRequestThrowsException()
	{
		$config   = new IceHawkConfig();
		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();

		$iceHawk->handleRequest();
	}

	public function testCanCallHandlerForGetRequest()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/ice_hawk_read',
			]
		);

		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures'
		);
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getEventListeners' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'Handler method for get request called.' );
	}

	public function testCanCallHandlerForPostRequest()
	{
		$config      = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/domain/ice_hawk_write',
			]
		);

		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures'
		);
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getEventListeners' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'Handler method for post request called.' );
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

		$uriRewriter = $this->getMockBuilder( RewritesUri::class )->setMethods( [ 'rewrite' ] )->getMock();
		$uriRewriter->expects( $this->once() )->method( 'rewrite' )->with( $requestInfo )->willReturn(
			new Redirect( '/domain/rewritten', HttpCode::MOVED_PERMANENTLY )
		);

		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn( __NAMESPACE__ );
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( $uriRewriter );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getEventListeners' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->assertContains( 'Location: /domain/rewritten', xdebug_get_headers() );
		$this->assertEquals( HttpCode::MOVED_PERMANENTLY, http_response_code() );
	}

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
		$getRequest    = new ReadRequest( $requestInfo, [ ] );
		$handlingEvent = new HandlingRequestEvent( $getRequest );
		$handledEvent  = new RequestWasHandledEvent( $getRequest );

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

		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures'
		);
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getEventListeners' )->willReturn( [ $eventListener ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation
	 */
	public function testInvalidUriRewriterFromConfigThrowsException()
	{
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new \stdClass() );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation
	 */
	public function testInvalidUriResolverFromConfigThrowsException()
	{
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new \stdClass() );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation
	 */
	public function testInvalidRequestInfoFromConfigThrowsException()
	{
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( new \stdClass() );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @param mixed $domainNamespace
	 *
	 * @dataProvider invalidDomainNamespaceProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidDomainNamespace
	 */
	public function testInvalidProjectNamespaceFromConfigThrowsException( $domainNamespace )
	{
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( RequestInfo::fromEnv() );
		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn( $domainNamespace );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	public function invalidDomainNamespaceProvider()
	{
		return [
			[ null ],
			[ false ],
			[ true ],
			[ 123 ],
			[ 12.3 ],
			[ '' ],
			[ [ ] ],
			[ new \stdClass() ],
		];
	}

	/**
	 * @param mixed $eventListeners
	 *
	 * @dataProvider invalidEventListenersProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection
	 */
	public function testInvalidEventListenersFromConfigThrowsException( $eventListeners )
	{
		$config = $this->getMockBuilder( ConfiguresIceHawk::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getReadUriResolver' )->willReturn( new ReadRequestResolver() );
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( RequestInfo::fromEnv() );
		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn( __NAMESPACE__ );
		$config->expects( $this->once() )->method( 'getEventListeners' )->willReturn( $eventListeners );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	public function invalidEventListenersProvider()
	{
		return [
			# invalid types, not traversable
			[ null ],
			[ false ],
			[ true ],
			[ 123 ],
			[ 12.3 ],
			[ '' ],
			[ new \stdClass() ],

			# invalid lists
			[
				[
					$this->getMockForAbstractClass( SubscribesToEvents::class ),
					new \stdClass(),
				],
			],
		];
	}
}
