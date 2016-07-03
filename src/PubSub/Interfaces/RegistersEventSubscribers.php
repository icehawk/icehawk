<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\PubSub\Interfaces;

/**
 * Interface RegistersEventSubscribers
 * @package IceHawk\IceHawk\PubSub\Interfaces
 */
interface RegistersEventSubscribers
{
	public function register( SubscribesToEvents $subscriber );
}