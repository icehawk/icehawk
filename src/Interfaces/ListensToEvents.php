<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ListensToEvents
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ListensToEvents
{
	public function acceptsEvent( ServesEventData $event ) : bool;

	public function notify( ServesEventData $event );
}
