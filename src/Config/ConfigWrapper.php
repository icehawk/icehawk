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

namespace IceHawk\IceHawk\Config;

use IceHawk\IceHawk\Interfaces\ConfiguresIceHawk;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\ProxiesRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use IceHawk\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use IceHawk\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToReadHandler;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Class IceHawkConfigWrapper
 * @package IceHawk\IceHawk\Config
 */
final class ConfigWrapper implements ConfiguresIceHawk
{
	/** @var ConfiguresIceHawk */
	private $config;

	/** @var array|\Traversable|RoutesToReadHandler[] */
	private $readRoutes;

	/** @var array|\Traversable|RoutesToWriteHandler[] */
	private $writeRoutes;

	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var array|SubscribesToEvents[] */
	private $eventSubscribers;

	/** @var RespondsFinallyToReadRequest */
	private $finalReadResponder;

	/** @var RespondsFinallyToWriteRequest */
	private $finalWriteResponder;

	/** @var  ProxiesRequest */
	private $requestProxy;

	public function __construct( ConfiguresIceHawk $config )
	{
		$this->config = $config;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		if ( $this->requestInfo === null )
		{
			$this->requestInfo = $this->config->getRequestInfo();
		}

		return $this->requestInfo;
	}

	/**
	 * @return array|RoutesToReadHandler[]|\Traversable
	 */
	public function getReadRoutes()
	{
		if ( $this->readRoutes === null )
		{
			$this->readRoutes = $this->config->getReadRoutes();
		}

		return $this->readRoutes;
	}

	/**
	 * @return array|RoutesToWriteHandler[]|\Traversable
	 */
	public function getWriteRoutes()
	{
		if ( $this->writeRoutes === null )
		{
			$this->writeRoutes = $this->config->getWriteRoutes();
		}

		return $this->writeRoutes;
	}

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array
	{
		if ( $this->eventSubscribers === null )
		{
			$this->eventSubscribers = $this->config->getEventSubscribers();
		}

		return $this->eventSubscribers;
	}

	public function getFinalReadResponder() : RespondsFinallyToReadRequest
	{
		if ( $this->finalReadResponder === null )
		{
			$this->finalReadResponder = $this->config->getFinalReadResponder();
		}

		return $this->finalReadResponder;
	}

	public function getFinalWriteResponder() : RespondsFinallyToWriteRequest
	{
		if ( $this->finalWriteResponder === null )
		{
			$this->finalWriteResponder = $this->config->getFinalWriteResponder();
		}

		return $this->finalWriteResponder;
	}

	public function getRequestProxy() : ProxiesRequest
	{
		if ( $this->requestProxy === null )
		{
			$this->requestProxy = $this->config->getRequestProxy();
		}

		return $this->requestProxy;
	}
}
