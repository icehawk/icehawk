<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\PubSub;

use IceHawk\IceHawk\PubSub\Exceptions\EventSubscriberMethodNotCallable;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;
use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class AbstractEventSubscriber
 * @package IceHawk\IceHawk\PubSub
 */
abstract class AbstractEventSubscriber implements SubscribesToEvents
{
	final public function acceptsEvent( CarriesEventData $event ) : bool
	{
		return in_array( get_class( $event ), $this->getAcceptedEvents() );
	}

	abstract protected function getAcceptedEvents() : array;

	/**
	 * @param CarriesEventData $event
	 *
	 * @throws EventSubscriberMethodNotCallable
	 */
	final public function notify( CarriesEventData $event )
	{
		$namespaceComponents = explode( "\\", get_class( $event ) );
		$methodName          = sprintf( 'when%s', preg_replace( "#Event$#", '', end( $namespaceComponents ) ) );

		if ( is_callable( [ $this, $methodName ] ) )
		{
			$this->{$methodName}( $event );
		}
		else
		{
			throw ( new EventSubscriberMethodNotCallable() )->withMethodName( $methodName );
		}
	}
}
