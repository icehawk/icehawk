<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\PubSub;

use Fortuneglobe\IceHawk\PubSub\Exceptions\EventSubscriberMethodNotCallable;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class AbstractEventSubscriber
 * @package Fortuneglobe\IceHawk\PubSub
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
