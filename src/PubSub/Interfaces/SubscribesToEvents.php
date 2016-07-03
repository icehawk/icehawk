<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\PubSub\Interfaces;

/**
 * Interface SubscribesToEvents
 * @package IceHawk\IceHawk\PubSub\Interfaces
 */
interface SubscribesToEvents
{
	public function acceptsEvent( CarriesEventData $event ) : bool;

	public function notify( CarriesEventData $event );
}