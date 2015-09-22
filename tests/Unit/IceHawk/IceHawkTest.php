<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\IceHawk;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\RequestWasHandledEvent;
use Fortuneglobe\IceHawk\IceHawk;
use Fortuneglobe\IceHawk\IceHawkConfig;
use Fortuneglobe\IceHawk\IceHawkDelegate;
use Fortuneglobe\IceHawk\Interfaces\ControlsHandlingBehaviour;
use Fortuneglobe\IceHawk\Interfaces\ListensToEvents;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkConfig;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;
use Fortuneglobe\IceHawk\Responses\Redirect;
use Fortuneglobe\IceHawk\UriResolver;
use Fortuneglobe\IceHawk\UriRewriter;

class IceHawkTest extends \PHPUnit_Framework_TestCase
{
	public function testDelegateMethodsWillBeCalledDuringInitialization()
	{
		$config   = new IceHawkConfig();
		$delegate = $this->prophesize( ControlsHandlingBehaviour::class );

		$delegate->setUpErrorHandling()->shouldBeCalled();
		$delegate->setUpSessionHandling()->shouldBeCalled();

		$iceHawk = new IceHawk( $config, $delegate->reveal() );
		$iceHawk->init();
	}

	public function testPublishesEventWhenInitializationIsDone()
	{
		$initEvent     = new IceHawkWasInitializedEvent();
		$eventListener = $this->getMockBuilder( ListensToEvents::class )
		                      ->setMethods( [ 'acceptsEvent', 'notify' ] )
		                      ->getMockForAbstractClass();

		$eventListener->expects( $this->once() )
		              ->method( 'acceptsEvent' )
		              ->with( $this->equalTo( $initEvent ) )
		              ->willReturn( true );

		$eventListener->expects( $this->once() )
		              ->method( 'notify' )
		              ->with( $this->equalTo( $initEvent ) );

		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();

		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn( __NAMESPACE__ );
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( RequestInfo::fromEnv() );
		$config->expects( $this->once() )
		       ->method( 'getEventListeners' )
		       ->willReturn( [ $eventListener ] );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri
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
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/ice_hawk_read'
			]
		);

		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures'
		);
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
		$config->expects( $this->once() )->method( 'getEventListeners' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->expectOutputString( 'Handler method for get request called.' );
	}

	public function testCanCallHandlerForPostRequest()
	{
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/domain/ice_hawk_write'
			]
		);

		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn(
			'Fortuneglobe\\IceHawk\\Tests\\Unit\\Fixtures'
		);
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
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
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();

		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/ice_hawk_rewrite'
			]
		);

		$uriRewriter = $this->getMockBuilder( RewritesUri::class )->setMethods( [ 'rewrite' ] )->getMock();
		$uriRewriter->expects( $this->once() )->method( 'rewrite' )->with( $requestInfo )->willReturn(
			new Redirect( '/domain/rewritten', Http::MOVED_PERMANENTLY )
		);

		$config->expects( $this->once() )->method( 'getDomainNamespace' )->willReturn( __NAMESPACE__ );
		$config->expects( $this->once() )->method( 'getRequestInfo' )->willReturn( $requestInfo );
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( $uriRewriter );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
		$config->expects( $this->once() )->method( 'getEventListeners' )->willReturn( [ ] );

		$delegate = new IceHawkDelegate();

		$iceHawk = new IceHawk( $config, $delegate );
		$iceHawk->init();
		$iceHawk->handleRequest();

		$this->assertContains( 'Location: /domain/rewritten', xdebug_get_headers() );
		$this->assertEquals( Http::MOVED_PERMANENTLY, http_response_code() );
	}

	public function testPublishesEventsWhenHandlingRequest()
	{
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI'    => '/domain/valid_read_test'
			]
		);

		$initEvent     = new IceHawkWasInitializedEvent();
		$handlingEvent = new HandlingRequestEvent( $requestInfo, new GetRequest( [ ] ) );
		$handledEvent  = new RequestWasHandledEvent( $requestInfo, new GetRequest( [ ] ) );

		$eventListener = $this->getMockBuilder( ListensToEvents::class )
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
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
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
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new \stdClass() );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation
	 */
	public function testInvalidUriResolverFromConfigThrowsException()
	{
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new \stdClass() );

		$iceHawk = new IceHawk( $config, new IceHawkDelegate() );
		$iceHawk->init();
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation
	 */
	public function testInvalidRequestInfoFromConfigThrowsException()
	{
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
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
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
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
		$config = $this->getMockBuilder( ServesIceHawkConfig::class )->getMockForAbstractClass();
		$config->expects( $this->once() )->method( 'getUriRewriter' )->willReturn( new UriRewriter() );
		$config->expects( $this->once() )->method( 'getUriResolver' )->willReturn( new UriResolver() );
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
					$this->getMockForAbstractClass( ListensToEvents::class ),
					new \stdClass(),
				]
			]
		];
	}
}
