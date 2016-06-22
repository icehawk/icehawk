<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Config\ConfigGuard;
use Fortuneglobe\IceHawk\Config\ConfigWrapper;
use Fortuneglobe\IceHawk\Constants\HttpMethod;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\InitializingIceHawkEvent;
use Fortuneglobe\IceHawk\Exceptions\InvalidEventSubscriberCollection;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\PubSub\Interfaces\PublishesEvents;
use Fortuneglobe\IceHawk\RequestHandlers\OptionsRequestHandler;
use Fortuneglobe\IceHawk\RequestHandlers\ReadRequestHandler;
use Fortuneglobe\IceHawk\RequestHandlers\WriteRequestHandler;
use Fortuneglobe\IceHawk\Responses\MethodNotImplemented;

/**
 * Class IceHawk
 *
 * @package Fortuneglobe\IceHawk
 */
final class IceHawk
{
	/** @var ConfiguresIceHawk */
	private $config;

	/** @var SetsUpEnvironment */
	private $setUpDelegate;

	/** @var PublishesEvents */
	private $eventPublisher;

	/**
	 * @param ConfiguresIceHawk $config
	 * @param SetsUpEnvironment $setUpDelegate
	 */
	public function __construct( ConfiguresIceHawk $config, SetsUpEnvironment $setUpDelegate )
	{
		$this->config         = $config;
		$this->setUpDelegate  = $setUpDelegate;
		$this->eventPublisher = new EventPublisher();
	}

	/**
	 * @throws InvalidEventSubscriberCollection
	 */
	public function init()
	{
		$this->setUpDelegate->setUpGlobalVars();

		$this->config = new ConfigWrapper( $this->config );

		$this->guardConfigIsValid();
		$this->registerEventSubscribers();

		$requestInfo = $this->config->getRequestInfo();

		$initializingEvent = new InitializingIceHawkEvent( $requestInfo );
		$this->eventPublisher->publish( $initializingEvent );

		$this->setUpDelegate->setUpSessionHandling( $requestInfo );
		$this->setUpDelegate->setUpErrorHandling( $requestInfo );

		$initializedEvent = new IceHawkWasInitializedEvent( $requestInfo );
		$this->eventPublisher->publish( $initializedEvent );
	}

	/**
	 * @throws InvalidEventSubscriberCollection
	 */
	private function guardConfigIsValid()
	{
		( new ConfigGuard( $this->config ) )->validate();
	}

	private function registerEventSubscribers()
	{
		foreach ( $this->config->getEventSubscribers() as $subscriber )
		{
			$this->eventPublisher->register( $subscriber );
		}
	}

	public function handleRequest()
	{
		$requestInfo = $this->config->getRequestInfo();

		if ( in_array( $requestInfo->getMethod(), HttpMethod::WRITE_METHODS ) )
		{
			$requestHandler = new WriteRequestHandler( $this->config, $this->eventPublisher );

			$requestHandler->handleRequest();
		}
		elseif ( in_array( $requestInfo->getMethod(), HttpMethod::READ_METHODS ) )
		{
			$requestHandler = new ReadRequestHandler( $this->config, $this->eventPublisher );

			$requestHandler->handleRequest();
		}
		elseif ( $requestInfo->getMethod() == HttpMethod::OPTIONS )
		{
			$requestHandler = new OptionsRequestHandler( $this->config, $this->eventPublisher );

			$requestHandler->handleRequest();
		}
		else
		{
			( new MethodNotImplemented( $requestInfo->getMethod() ) )->respond();
		}
	}
}
