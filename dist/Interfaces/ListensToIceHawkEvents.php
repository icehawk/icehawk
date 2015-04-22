<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ListensToIceHawkEvents
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ListensToIceHawkEvents
{
	/**
	 * @return array
	 */
	public function getAcceptedEvents();

	/**
	 * @param ServesIceHawkEventData $event
	 *
	 * @return bool
	 */
	public function acceptsEvent( ServesIceHawkEventData $event );

	/**
	 * @param ServesIceHawkEventData $event
	 */
	public function notify( ServesIceHawkEventData $event );
}
