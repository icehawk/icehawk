<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ObservesEvents
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ObservesEvents
{
	/**
	 * @param ServesEventData $event
	 */
	public function notify( ServesEventData $event );
}