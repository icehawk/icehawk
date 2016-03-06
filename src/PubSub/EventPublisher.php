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

	private function __construct()
	{
		$this->subscribers = [ ];
	}

	private function __clone()
	{
	}

	public static function singleton() : self
	{
		static $instance = null;

		if ( is_null( $instance ) )
		{
			$instance = new self();
		}

		return $instance;
	}

	public function register( SubscribesToEvents $subscriber )
	{
		if ( !in_array( $subscriber, $this->subscribers ) )
		{
			$this->subscribers[] = $subscriber;
		}
	}

	/**
	 * @param CarriesEventData $event
	 *
	 * @return bool
	 */
	public function publish( CarriesEventData $event )
	{
		$eventPublished = false;

		foreach ( $this->subscribers as $subscriber )
		{
			if ( $subscriber->acceptsEvent( $event ) )
			{
				$subscriber->notify( $event );
				$eventPublished = true;
			}
		}

		return $eventPublished;
	}
}