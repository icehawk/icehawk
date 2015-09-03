<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Events;

use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\IceHawkEventListener;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\GetRequest;

class IceHawkEventListenerTest extends \PHPUnit_Framework_TestCase
{
	public function testCanCheckForExceptedEvents()
	{
		$initEvent     = new IceHawkWasInitializedEvent();
		$handlingEvent = new HandlingRequestEvent( RequestInfo::fromEnv(), new GetRequest( [ ] ) );

		$mock = $this->getMockBuilder( IceHawkEventListener::class )
		             ->setMethods( [ 'getAcceptedEvents' ] )
		             ->getMockForAbstractClass();

		$mock->expects( $this->exactly( 2 ) )
		     ->method( 'getAcceptedEvents' )
		     ->willReturn( [ IceHawkWasInitializedEvent::class ] );

		/** @var IceHawkEventListener $mock */
		$this->assertTrue( $mock->acceptsEvent( $initEvent ) );
		$this->assertFalse( $mock->acceptsEvent( $handlingEvent ) );
	}

	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\EventListenerMethodNotCallable
	 */
	public function testNotImplementedListenerMethodThrowsException()
	{
		$initEvent = new IceHawkWasInitializedEvent();

		$mock = $this->getMockBuilder( IceHawkEventListener::class )
		             ->setMethods( [ 'getAcceptedEvents' ] )
		             ->getMockForAbstractClass();

		/** @var IceHawkEventListener $mock */
		$mock->notify( $initEvent );
	}

	public function testCanHandleEventInListenerMethod()
	{
		$initEvent = new IceHawkWasInitializedEvent();

		$mock = $this->getMockBuilder( IceHawkEventListener::class )
		             ->setMethods( [ 'getAcceptedEvents', 'whenIceHawkWasInitialized' ] )
		             ->getMockForAbstractClass();

		$mock->expects( $this->once() )->method( 'whenIceHawkWasInitialized' )->with( $initEvent );

		/** @var IceHawkEventListener $mock */
		$mock->notify( $initEvent );
	}
}
