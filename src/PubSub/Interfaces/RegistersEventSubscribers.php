<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\PubSub\Interfaces;

/**
 * Interface RegistersEventSubscribers
 * @package Fortuneglobe\IceHawk\PubSub\Interfaces
 */
interface RegistersEventSubscribers
{
	public function register( SubscribesToEvents $subscriber );
}