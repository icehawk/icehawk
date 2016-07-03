<?php
namespace IceHawk\IceHawk\Tests\Unit\Fixtures;

use IceHawk\IceHawk\Events\IceHawkWasInitializedEvent;
use IceHawk\IceHawk\Events\WriteRequestWasHandledEvent;
use IceHawk\IceHawk\PubSub\AbstractEventSubscriber;

/**
 * Class TestEventSubscriber
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures
 */
class TestEventSubscriber extends AbstractEventSubscriber
{
	protected function getAcceptedEvents() : array
	{
		return [ IceHawkWasInitializedEvent::class, WriteRequestWasHandledEvent::class ];
	}
}