<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures\PubSub;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Events\HandlingReadRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\PubSub\Exceptions\EventSubscriberMethodNotCallable;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestEventSubscriber;

/**
 * Class EventSubscriberTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures\PubSub
 */
class EventSubscriberTest extends \PHPUnit_Framework_TestCase
{
	public function testCanCheckForAcceptedEvents()
	{
		$initEvent     = new IceHawkWasInitializedEvent( RequestInfo::fromEnv() );
		$handlingEvent =
			new HandlingReadRequestEvent( new ReadRequest( RequestInfo::fromEnv(), new ReadRequestInput( [ ] ) ) );

		$mock = $this->getMockBuilder( TestEventSubscriber::class )
		             ->setMethods( [ 'getAcceptedEvents', 'whenIceHawkWasInitialized' ] )
		             ->getMockForAbstractClass();

		$mock->expects( $this->exactly( 2 ) )
		     ->method( 'getAcceptedEvents' )
		     ->willReturn( [ IceHawkWasInitializedEvent::class ] );

		/** @var TestEventSubscriber $mock */
		$this->assertTrue( $mock->acceptsEvent( $initEvent ) );
		$this->assertFalse( $mock->acceptsEvent( $handlingEvent ) );
	}

	public function testNotImplementedListenerMethodThrowsException()
	{
		$initEvent = new IceHawkWasInitializedEvent( RequestInfo::fromEnv() );
	
		try 
		{
			$mock = $this->getMockBuilder( TestEventSubscriber::class )
			             ->setMethods( [ 'getAcceptedEvents' ] )
			             ->getMockForAbstractClass();

			/** @var TestEventSubscriber $mock */
			$mock->notify( $initEvent );
			
			$this->fail('No Exception thrown');
		} 
		catch ( EventSubscriberMethodNotCallable $ex )
		{
			$this->assertEquals( 'whenIceHawkWasInitialized', $ex->getMethodName() );
		}
		catch ( \Throwable $throwable )
		{
			$this->fail( 'Wrong exception thrown');
		}
	}

	public function testCanHandleEventInListenerMethod()
	{
		$initEvent = new IceHawkWasInitializedEvent( RequestInfo::fromEnv() );

		$mock = $this->getMockBuilder( TestEventSubscriber::class )
		             ->setMethods( [ 'getAcceptedEvents', 'whenIceHawkWasInitialized' ] )
		             ->getMockForAbstractClass();

		$mock->expects( $this->once() )->method( 'whenIceHawkWasInitialized' )->with( $initEvent );

		/** @var TestEventSubscriber $mock */
		$mock->notify( $initEvent );
	}
}