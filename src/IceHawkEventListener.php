<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\EventListenerMethodNotCallable;
use Fortuneglobe\IceHawk\Interfaces\ListensToIceHawkEvents;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkEventData;

/**
 * Class IceHawkEventListener
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class IceHawkEventListener implements ListensToIceHawkEvents
{
	/**
	 * @param ServesIceHawkEventData $event
	 *
	 * @return bool
	 */
	public function acceptsEvent( ServesIceHawkEventData $event )
	{
		return in_array( get_class( $event ), $this->getAcceptedEvents() );
	}

	/**
	 * @param ServesIceHawkEventData $event
	 *
	 * @throws EventListenerMethodNotCallable
	 */
	public function notify( ServesIceHawkEventData $event )
	{
		$namespaceComponents = explode( "\\", get_class( $event ) );
		$methodName          = sprintf( 'when%s', preg_replace( "#Event$#", '', end( $namespaceComponents ) ) );

		if ( is_callable( [ $this, $methodName ] ) )
		{
			$this->{$methodName}( $event );
		}
		else
		{
			throw new EventListenerMethodNotCallable( $methodName );
		}
	}
}
