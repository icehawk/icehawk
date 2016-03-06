<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\PubSub\Interfaces;

/**
 * Interface PublishesEvents
 * @package Fortuneglobe\IceHawk\PubSub\Interfaces
 */
interface PublishesEvents
{
	public function publish( CarriesEventData $event ) : bool;
}