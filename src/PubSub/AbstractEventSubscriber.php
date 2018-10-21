<?php declare(strict_types=1);
/**
 * Copyright (c) 2017 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\PubSub;

use IceHawk\IceHawk\PubSub\Exceptions\EventSubscriberMethodNotCallable;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;
use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use function get_class;
use function in_array;
use function is_callable;

/**
 * Class AbstractEventSubscriber
 * @package IceHawk\IceHawk\PubSub
 */
abstract class AbstractEventSubscriber implements SubscribesToEvents
{
	final public function acceptsEvent( CarriesEventData $event ) : bool
	{
		return in_array( get_class( $event ), $this->getAcceptedEvents(), true );
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
		$lastComponent       = (string)end( $namespaceComponents );
		$methodName          = sprintf( 'when%s', preg_replace( '#Event$#', '', $lastComponent ) );

		if ( is_callable( [$this, $methodName] ) )
		{
			$this->{$methodName}( $event );
		}
		else
		{
			throw (new EventSubscriberMethodNotCallable())->withMethodName( $methodName );
		}
	}
}
