<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\PubSub\Interfaces\CarriesEventData;
use IceHawk\IceHawk\PubSub\Interfaces\PublishesEvents;

/**
 * Class AbstractRequestHandler
 * @package IceHawk\IceHawk\RequestHandlers
 */
abstract class AbstractRequestHandler
{
	/** @var ConfiguresIceHawk */
	protected $config;

	/** @var PublishesEvents */
	private $eventPublisher;

	/**
	 * @param ConfiguresIceHawk $config
	 * @param PublishesEvents   $eventPublisher
	 */
	final public function __construct( ConfiguresIceHawk $config, PublishesEvents $eventPublisher )
	{
		$this->config         = $config;
		$this->eventPublisher = $eventPublisher;
	}

	final protected function publishEvent( CarriesEventData $event )
	{
		$this->eventPublisher->publish( $event );
	}

	abstract public function handleRequest();
}