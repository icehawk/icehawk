<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ListensToEvents;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkConfig;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class IceHawkConfigWrapper
 *
 * @package Fortuneglobe\IceHawk
 */
final class IceHawkConfigWrapper implements ServesIceHawkConfig
{
	/** @var ServesRequestInfo */
	private $requestInfo;

	/** @var ResolvesUri */
	private $uriResolver;

	/** @var RewritesUri */
	private $uriRewriter;

	/** @var array|ListensToEvents */
	private $eventListeners;

	/** @var string */
	private $domainNamespace;

	/**
	 * @param ServesIceHawkConfig $config
	 */
	public function __construct( ServesIceHawkConfig $config )
	{
		$this->requestInfo     = $config->getRequestInfo();
		$this->uriResolver     = $config->getUriResolver();
		$this->uriRewriter     = $config->getUriRewriter();
		$this->eventListeners  = $config->getEventListeners();
		$this->domainNamespace = $config->getDomainNamespace();
	}

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo()
	{
		return $this->requestInfo;
	}

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver()
	{
		return $this->uriResolver;
	}

	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter()
	{
		return $this->uriRewriter;
	}

	/**
	 * @return array|ListensToEvents
	 */
	public function getEventListeners()
	{
		return $this->eventListeners;
	}

	/**
	 * @return string
	 */
	public function getDomainNamespace()
	{
		return $this->domainNamespace;
	}
}