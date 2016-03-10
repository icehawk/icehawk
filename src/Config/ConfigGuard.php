<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Config;

use Fortuneglobe\IceHawk\Exceptions\InvalidDomainNamespace;
use Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ResolvesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
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
	 * @throws InvalidDomainNamespace
	 * @throws InvalidEventListenerCollection
	 * @throws InvalidRequestInfoImplementation
	 * @throws InvalidUriResolverImplementation
	 * @throws InvalidUriRewriterImplementation
	 */
	public function guardConfigIsValid()
	{
		$this->guardUriRewriterIsValid();
		$this->guardUriResolverIsValid();
		$this->guardRequestInfoIsValid();
		$this->guardDomainNamespaceIsValid();
		$this->guardEventListenersAreValid();
	}

	/**
	 * @throws InvalidUriRewriterImplementation
	 */
	private function guardUriRewriterIsValid()
	{
		$uriRewriter = $this->config->getUriRewriter();

		if ( !($uriRewriter instanceof RewritesUri) )
		{
			throw new InvalidUriRewriterImplementation();
		}
	}

	/**
	 * @throws InvalidUriResolverImplementation
	 */
	private function guardUriResolverIsValid()
	{
		$uriResolver = $this->config->getReadUriResolver();

		if ( !($uriResolver instanceof ResolvesReadRequest) )
		{
			throw new InvalidUriResolverImplementation();
		}
	}

	/**
	 * @throws InvalidRequestInfoImplementation
	 */
	private function guardRequestInfoIsValid()
	{
		$requestInfo = $this->config->getRequestInfo();

		if ( !($requestInfo instanceof ProvidesRequestInfo) )
		{
			throw new InvalidRequestInfoImplementation();
		}
	}

	/**
	 * @throws InvalidDomainNamespace
	 */
	private function guardDomainNamespaceIsValid()
	{
		$domainNamespace = $this->config->getHandlerRootNamespace();

		if ( empty($domainNamespace) || !is_string( $domainNamespace ) )
		{
			throw new InvalidDomainNamespace();
		}
	}

	/**
	 * @throws InvalidEventListenerCollection
	 */
	private function guardEventListenersAreValid()
	{
		$eventListeners = $this->config->getEventSubscribers();

		if ( !is_array( $eventListeners ) && !($eventListeners instanceof \Traversable) )
		{
			throw new InvalidEventListenerCollection();
		}
		else
		{
			foreach ( $eventListeners as $eventListener )
			{
				if ( !($eventListener instanceof SubscribesToEvents) )
				{
					throw new InvalidEventListenerCollection();
				}
			}
		}
	}
}