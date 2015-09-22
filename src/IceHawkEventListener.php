<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\EventListenerMethodNotCallable;
use Fortuneglobe\IceHawk\Interfaces\ListensToEvents;
use Fortuneglobe\IceHawk\Interfaces\ServesEventData;

/**
 * Class EventListener
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class EventListener implements ListensToEvents
{
	/**
	 * @param ServesEventData $event
	 *
	 * @return bool
	 */
	public function acceptsEvent( ServesEventData $event )
	{
		return in_array( get_class( $event ), $this->getAcceptedEvents() );
	}

	/**
	 * @param ServesEventData $event
	 *
	 * @throws EventListenerMethodNotCallable
	 */
	public function notify( ServesEventData $event )
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
