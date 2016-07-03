<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Defaults\Traits;

use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Trait DefaultEventSubscribing
 * @package IceHawk\IceHawk\Defaults\Traits
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