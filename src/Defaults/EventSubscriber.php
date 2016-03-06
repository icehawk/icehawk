<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Defaults;

use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\RedirectingEvent;
use Fortuneglobe\IceHawk\Events\RequestWasHandledEvent;
use Fortuneglobe\IceHawk\Events\UncaughtExceptionWasThrownEvent;
use Fortuneglobe\IceHawk\PubSub\AbstractEventSubscriber;

/**
 * Class EventSubscriber
 * @package Fortuneglobe\IceHawk\Defaults
 */
class EventSubscriber extends AbstractEventSubscriber
{
	protected function getAcceptedEvents() : array
	{
		return [
			IceHawkWasInitializedEvent::class,
			RedirectingEvent::class,
			HandlingRequestEvent::class,
			RequestWasHandledEvent::class,
			UncaughtExceptionWasThrownEvent::class,
		];
	}

	/**
	 * @param IceHawkWasInitializedEvent $event
	 */
	protected function whenIceHawkWasInitialized( IceHawkWasInitializedEvent $event )
	{
	}

	/**
	 * @param RedirectingEvent $event
	 */
	protected function whenRedirecting( RedirectingEvent $event )
	{
	}

	/**
	 * @param HandlingRequestEvent $event
	 */
	protected function whenHandlingRequest( HandlingRequestEvent $event )
	{
	}

	/**
	 * @param RequestWasHandledEvent $event
	 */
	protected function whenRequestWasHandled( RequestWasHandledEvent $event )
	{
	}

	/**
	 * @param UncaughtExceptionWasThrownEvent $event
	 *
	 * @throws \Throwable
	 */
	protected function whenUncaughtExceptionWasThrown( UncaughtExceptionWasThrownEvent $event )
	{
		throw $event->getThrowable();
	}
}