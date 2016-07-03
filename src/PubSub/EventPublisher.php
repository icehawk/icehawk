<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\PubSub;

use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;
use Fortuneglobe\IceHawk\PubSub\Interfaces\PublishesEvents;
use Fortuneglobe\IceHawk\PubSub\Interfaces\RegistersEventSubscribers;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class EventPublisher
 * @package Fortuneglobe\IceHawk\PubSub
 */
final class EventPublisher implements RegistersEventSubscribers, PublishesEvents
{
	/** @var array|SubscribesToEvents[] */
	private $subscribers;

	public function __construct()
	{
		$this->subscribers = [ ];
	}

	public function register( SubscribesToEvents $subscriber )
	{
		if ( !in_array( $subscriber, $this->subscribers ) )
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