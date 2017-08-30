<?php declare(strict_types = 1);
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

use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;
use IceHawk\IceHawk\PubSub\Interfaces\PublishesEvents;
use IceHawk\IceHawk\PubSub\Interfaces\RegistersEventSubscribers;
use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class EventPublisher
 * @package IceHawk\IceHawk\PubSub
 */
final class EventPublisher implements RegistersEventSubscribers, PublishesEvents
{
	/** @var array|SubscribesToEvents[] */
	private $subscribers;

	public function __construct()
	{
		$this->subscribers = [];
	}

	public function register( SubscribesToEvents $subscriber )
	{
		if ( !in_array( $subscriber, $this->subscribers, false ) )
		{
			$this->subscribers[] = $subscriber;
		}
	}

	public function publish( CarriesEventData $event )
	{
		foreach ( $this->subscribers as $subscriber )
		{
			if ( $subscriber->acceptsEvent( $event ) )
			{
				$subscriber->notify( $event );
			}
		}
	}
}
