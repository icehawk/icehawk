<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\PubSub\Interfaces;

/**
 * Interface PublishesEvents
 * @package IceHawk\IceHawk\PubSub\Interfaces
 */
interface PublishesEvents
{
	public function publish( CarriesEventData $event );
}