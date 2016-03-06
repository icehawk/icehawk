<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Events;

use Fortuneglobe\IceHawk\AbstractEventListener;
use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;

class IceHawkEventListenerTest extends \PHPUnit_Framework_TestCase
{
	public function testCanCheckForExceptedEvents()
	{
		$requestInfo   = RequestInfo::fromEnv();
		$initEvent     = new IceHawkWasInitializedEvent( $requestInfo );
		$getRequest    = new GetRequest( $requestInfo, [ ] );
		$handlingEvent = new HandlingRequestEvent( $getRequest );

		$mock = $this->getMockBuilder( AbstractEventListener::class )
		             ->setMethods( [ 'getAcceptedEvents' ] )
		             ->getMockForAbstractClass();

		$mock->expects( $this->exactly( 2 ) )
		     ->method( 'getAcceptedEvents' )
		     ->willReturn( [ IceHawkWasInitializedEvent::class ] );

		/** @var AbstractEventListener $mock */
		$this->assertTrue( $mock->acceptsEvent( $initEvent ) );
		$this->assertFalse( $mock->acceptsEvent( $handlingEvent ) );
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\EventListenerMethodNotCallable
	 */
	public function testNotImplementedListenerMethodThrowsException()
	{
		$requestInfo = RequestInfo::fromEnv();
		$initEvent   = new IceHawkWasInitializedEvent( $requestInfo );

		$mock = $this->getMockBuilder( AbstractEventListener::class )
		             ->setMethods( [ 'getAcceptedEvents' ] )
		             ->getMockForAbstractClass();

		/** @var AbstractEventListener $mock */
		$mock->notify( $initEvent );
	}

	public function testCanHandleEventInListenerMethod()
	{
		$requestInfo = RequestInfo::fromEnv();
		$initEvent   = new IceHawkWasInitializedEvent( $requestInfo );

		$mock = $this->getMockBuilder( AbstractEventListener::class )
		             ->setMethods( [ 'getAcceptedEvents', 'whenIceHawkWasInitialized' ] )
		             ->getMockForAbstractClass();

		$mock->expects( $this->once() )->method( 'whenIceHawkWasInitialized' )->with( $initEvent );

		/** @var AbstractEventListener $mock */
		$mock->notify( $initEvent );
	}
}
