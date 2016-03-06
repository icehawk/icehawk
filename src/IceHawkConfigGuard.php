<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\InvalidDomainNamespace;
use Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation;
use Fortuneglobe\IceHawk\Interfaces\ListensToEvents;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkConfig;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class IceHawkConfigGuard
 *
 * @package Fortuneglobe\IceHawk
 */
final class IceHawkConfigGuard
{
	/** @var ServesIceHawkConfig */
	private $config;

	/**
	 * @param ServesIceHawkConfig $config
	 */
	public function __construct( ServesIceHawkConfig $config )
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
		$uriResolver = $this->config->getUriResolver();

		if ( !($uriResolver instanceof ResolvesUri) )
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

		if ( !($requestInfo instanceof ServesRequestInfo) )
		{
			throw new InvalidRequestInfoImplementation();
		}
	}

	/**
	 * @throws InvalidDomainNamespace
	 */
	private function guardDomainNamespaceIsValid()
	{
		$domainNamespace = $this->config->getDomainNamespace();

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
				if ( !($eventListener instanceof ListensToEvents) )
				{
					throw new InvalidEventListenerCollection();
				}
			}
		}
	}
}