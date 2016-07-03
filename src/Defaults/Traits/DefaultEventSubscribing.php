<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Trait DefaultEventSubscribing
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultEventSubscribing
{
	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array
	{
		return [ ];
	}
}