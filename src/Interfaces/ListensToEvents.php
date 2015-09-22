<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ListensToEvents
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ListensToEvents
{
	/**
	 * @return array
	 */
	public function getAcceptedEvents();

	/**
	 * @param ServesEventData $event
	 *
	 * @return bool
	 */
	public function acceptsEvent( ServesEventData $event );

	/**
	 * @param ServesEventData $event
	 */
	public function notify( ServesEventData $event );
}
