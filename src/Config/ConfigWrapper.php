<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Config;

use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToReadRequest;
use Fortuneglobe\IceHawk\Interfaces\RespondsFinallyToWriteRequest;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToWriteHandler;

/**
 * Class IceHawkConfigWrapper
 * @package Fortuneglobe\IceHawk\Config
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
}