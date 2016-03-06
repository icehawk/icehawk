<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\PubSub\Interfaces;

/**
 * Interface SubscribesToEvents
 * @package Fortuneglobe\IceHawk\PubSub\Interfaces
 */
interface SubscribesToEvents
{
	public function acceptsEvent( CarriesEventData $event ) : bool;

	public function notify( CarriesEventData $event );
}