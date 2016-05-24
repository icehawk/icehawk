<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Config;

use Fortuneglobe\IceHawk\Exceptions\InvalidEventSubscriberCollection;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class IceHawkConfigGuard
 * @package Fortuneglobe\IceHawk\Config
 */
final class ConfigGuard
{
	/** @var ConfiguresIceHawk */
	private $config;

	/**
	 * @param ConfiguresIceHawk $config
	 */
	public function __construct( ConfiguresIceHawk $config )
	{
		$this->config = $config;
	}

	/**
	 * @throws InvalidEventSubscriberCollection
	 */
	public function validate()
	{
		$this->guardEventSubscribersAreValid();
	}

	/**
	 * @throws InvalidEventSubscriberCollection
	 */
	private function guardEventSubscribersAreValid()
	{
		$eventSubscribers = $this->config->getEventSubscribers();

		$invalidSubscribers = array_filter(
			$eventSubscribers,
			function ( $subscriber )
			{
				return (!is_object( $subscriber ) || !($subscriber instanceof SubscribesToEvents));
			}
		);

		if ( !empty($invalidSubscribers) )
		{
			throw ( new InvalidEventSubscriberCollection() )->withInvalidKeys( array_keys( $invalidSubscribers ) );
		}
	}
}