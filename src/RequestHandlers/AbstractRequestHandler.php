<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;
use Fortuneglobe\IceHawk\PubSub\Interfaces\PublishesEvents;

/**
 * Class AbstractRequestHandler
 * @package Fortuneglobe\IceHawk\RequestHandlers
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