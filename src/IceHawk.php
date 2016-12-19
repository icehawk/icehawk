<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk;

use IceHawk\IceHawk\Config\ConfigGuard;
use IceHawk\IceHawk\Config\ConfigWrapper;
use IceHawk\IceHawk\Constants\HttpMethod;
use IceHawk\IceHawk\Events\IceHawkWasInitializedEvent;
use IceHawk\IceHawk\Events\InitializingIceHawkEvent;
use IceHawk\IceHawk\Exceptions\InvalidEventSubscriberCollection;
use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\SetsUpEnvironment;
use IceHawk\IceHawk\PubSub\EventPublisher;
use IceHawk\IceHawk\PubSub\Interfaces\PublishesEvents;
use IceHawk\IceHawk\RequestHandlers\OptionsRequestHandler;
use IceHawk\IceHawk\RequestHandlers\ReadRequestHandler;
use IceHawk\IceHawk\RequestHandlers\WriteRequestHandler;
use IceHawk\IceHawk\Responses\MethodNotImplemented;
use IceHawk\IceHawk\Routing\RequestBypasser;

/**
 * Class IceHawk
 * @package IceHawk\IceHawk
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
		(new ConfigGuard( $this->config ))->validate();
	}

	private function registerEventSubscribers()
	{
		foreach ( $this->config->getEventSubscribers() as $subscriber )
		{
			$this->eventPublisher->register( $subscriber );
		}
	}

	private function getFinalRequestInfo() : ProvidesRequestInfo
	{
		$requestInfo   = $this->config->getRequestInfo();
		$bypassHandler = new RequestBypasser();

		foreach ( $this->config->getRequestBypasses() as $requestBypass )
		{
			$bypassHandler->addRequestBypass( $requestBypass );
		}

		return $bypassHandler->bypassRequest( $requestInfo );
	}

	public function handleRequest()
	{
		$requestInfo = $this->getFinalRequestInfo();

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
			(new MethodNotImplemented( $requestInfo->getMethod() ))->respond();
		}
	}
}
