<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Config;

use IceHawk\IceHawk\Exceptions\InvalidEventSubscriberCollection;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class IceHawkConfigGuard
 * @package IceHawk\IceHawk\Config
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