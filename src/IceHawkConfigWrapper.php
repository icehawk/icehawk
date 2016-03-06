<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\PubSub\Interfaces\SubscribesToEvents;

/**
 * Class IceHawkConfigWrapper
 *
 * @package Fortuneglobe\IceHawk
 */
final class IceHawkConfigWrapper implements ConfiguresIceHawk
{
	/** @var ServesRequestInfo */
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
		$this->domainNamespace  = $config->getDomainNamespace();
	}

	public function getRequestInfo() : ServesRequestInfo
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

	public function getDomainNamespace() : string
	{
		return $this->domainNamespace;
	}
}