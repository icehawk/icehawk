<?php declare(strict_types=1);
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

namespace IceHawk\IceHawk\Tests\Unit\Fixtures\PubSub;

use IceHawk\IceHawk\Defaults\Cookies;
use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\HandlingReadRequestEvent;
use IceHawk\IceHawk\Events\IceHawkWasInitializedEvent;
use IceHawk\IceHawk\PubSub\Exceptions\EventSubscriberMethodNotCallable;
use IceHawk\IceHawk\Requests\ReadRequest;
use IceHawk\IceHawk\Requests\ReadRequestInput;
use IceHawk\IceHawk\Tests\Unit\Fixtures\TestEventSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * Class EventSubscriberTest
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures\PubSub
 */
class EventSubscriberTest extends TestCase
{
	public function testCanCheckForAcceptedEvents()
	{
		$initEvent     = new IceHawkWasInitializedEvent( RequestInfo::fromEnv(), new Cookies( [] ) );
		$readRequest   = new ReadRequest( RequestInfo::fromEnv(), new Cookies( [] ), new ReadRequestInput( [] ) );
		$handlingEvent = new HandlingReadRequestEvent( $readRequest );

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
		$initEvent = new IceHawkWasInitializedEvent( RequestInfo::fromEnv(), new Cookies( [] ) );

		try
		{
			$mock = $this->getMockBuilder( TestEventSubscriber::class )
			             ->setMethods( [ 'getAcceptedEvents' ] )
			             ->getMockForAbstractClass();

			/** @var TestEventSubscriber $mock */
			$mock->notify( $initEvent );

			$this->fail( 'No Exception thrown' );
		}
		catch ( EventSubscriberMethodNotCallable $ex )
		{
			$this->assertEquals( 'whenIceHawkWasInitialized', $ex->getMethodName() );
		}
		catch ( \Throwable $throwable )
		{
			$this->fail( 'Wrong exception thrown' );
		}
	}

	public function testCanHandleEventInListenerMethod()
	{
		$initEvent = new IceHawkWasInitializedEvent( RequestInfo::fromEnv(), new Cookies( [] ) );

		$mock = $this->getMockBuilder( TestEventSubscriber::class )
		             ->setMethods( [ 'getAcceptedEvents', 'whenIceHawkWasInitialized' ] )
		             ->getMockForAbstractClass();

		$mock->expects( $this->once() )->method( 'whenIceHawkWasInitialized' )->with( $initEvent );

		/** @var TestEventSubscriber $mock */
		$mock->notify( $initEvent );
	}
}
