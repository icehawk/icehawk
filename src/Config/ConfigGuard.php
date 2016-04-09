<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Config;

use Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection;
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
	 * @throws InvalidEventListenerCollection
	 */
	public function guardConfigIsValid()
	{
		$this->guardEventSubscribersAreValid();
	}

	/**
	 * @throws InvalidEventListenerCollection
	 */
	private function guardEventSubscribersAreValid()
	{
		$eventListeners = $this->config->getEventSubscribers();

		$invalidSubscribers = array_filter(
			$eventListeners,
			function ( $subscriber )
			{
				return (!is_object( $subscriber ) || !($subscriber instanceof SubscribesToEvents));
			}
		);

		if ( !empty($invalidSubscribers) )
		{
			throw ( new InvalidEventListenerCollection() )->withInvalidKeys( array_keys( $invalidSubscribers ) );
		}
	}
}