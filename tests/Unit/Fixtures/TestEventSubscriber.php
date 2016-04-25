<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\WriteRequestWasHandledEvent;
use Fortuneglobe\IceHawk\PubSub\AbstractEventSubscriber;

/**
 * Class TestEventSubscriber
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestEventSubscriber extends AbstractEventSubscriber
{
	protected function getAcceptedEvents() : array
	{
		return [ IceHawkWasInitializedEvent::class, WriteRequestWasHandledEvent::class ];
	}
}