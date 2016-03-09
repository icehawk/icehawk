<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Config;

use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class IceHawkConfigWrapper
 * @package Fortuneglobe\IceHawk\Config
 */
final class ConfigWrapper implements ConfiguresIceHawk
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var ResolvesUri */
	private $uriResolver;

	/** @var RewritesUri */
	private $uriRewriter;

	/** @var array|SubscribesToEvents[] */
	private $eventSubscribers;

	/** @var string */
	private $domainNamespace;

	public function __construct( ConfiguresIceHawk $config )
	{
		$this->requestInfo      = $config->getRequestInfo();
		$this->uriResolver      = $config->getUriResolver();
		$this->uriRewriter      = $config->getUriRewriter();
		$this->eventSubscribers = $config->getEventSubscribers();
		$this->domainNamespace  = $config->getHandlerRootNamespace();
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getUriResolver() : ResolvesUri
	{
		return $this->uriResolver;
	}

	public function getUriRewriter() : RewritesUri
	{
		return $this->uriRewriter;
	}

	/**
	 * @return array|SubscribesToEvents[]
	 */
	public function getEventSubscribers() : array
	{
		return $this->eventSubscribers;
	}

	public function getHandlerRootNamespace() : string
	{
		return $this->domainNamespace;
	}
}