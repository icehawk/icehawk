<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Builders\RequestBuilder;
use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\RequestWasHandledEvent;
use Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection;
use Fortuneglobe\IceHawk\Exceptions\InvalidProjectNamespace;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation;
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\HandlesIceHawkTasks;
use Fortuneglobe\IceHawk\Interfaces\ListensToIceHawkEvents;
use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkConfig;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkEventData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;

/**
 * Class IceHawk
 *
 * @package Fortuneglobe\IceHawk
 */
final class IceHawk
{

	/** @var RewritesUri */
	private $uriRewriter;

	/** @var ResolvesUri */
	private $uriResolver;

	/** @var string */
	private $projectNamespace;

	/** @var array|ListensToIceHawkEvents[] */
	private $eventListeners;

	/** @var ServesRequestInfo */
	private $requestInfo;

	/** @var HandlesIceHawkTasks */
	private $delegate;

	/**
	 * @param ServesIceHawkConfig $config
	 * @param HandlesIceHawkTasks $delegate
	 */
	public function __construct( ServesIceHawkConfig $config, HandlesIceHawkTasks $delegate )
	{
		$this->uriRewriter      = $config->getUriRewriter();
		$this->uriResolver      = $config->getUriResolver();
		$this->projectNamespace = $config->getProjectNamespace();
		$this->eventListeners   = $config->getEventListeners();
		$this->requestInfo      = $config->getRequestInfo();
		$this->delegate         = $delegate;
	}

	/**
	 * @throws InvalidEventListenerCollection
	 * @throws InvalidProjectNamespace
	 * @throws InvalidRequestInfoImplementation
	 * @throws InvalidUriResolverImplementation
	 * @throws InvalidUriRewriterImplementation
	 */
	public function init()
	{
		$this->delegate->configureErrorHandling();
		$this->delegate->configureSession();

		$this->guardConfigIsValid();

		$initializedEvent = new IceHawkWasInitializedEvent();
		$this->publishEvent( $initializedEvent );
	}

	/**
	 * @throws InvalidEventListenerCollection
	 * @throws InvalidProjectNamespace
	 * @throws InvalidRequestInfoImplementation
	 * @throws InvalidUriResolverImplementation
	 * @throws InvalidUriRewriterImplementation
	 */
	private function guardConfigIsValid()
	{
		if ( !($this->uriRewriter instanceof RewritesUri) )
		{
			throw new InvalidUriRewriterImplementation();
		}

		if ( !($this->uriResolver instanceof ResolvesUri) )
		{
			throw new InvalidUriResolverImplementation();
		}

		if ( !($this->requestInfo instanceof ServesRequestInfo) )
		{
			throw new InvalidRequestInfoImplementation();
		}

		if ( empty($this->projectNamespace) || !is_string( $this->projectNamespace ) )
		{
			throw new InvalidProjectNamespace();
		}

		if ( !is_array( $this->eventListeners ) && !($this->eventListeners instanceof \Traversable) )
		{
			throw new InvalidEventListenerCollection();
		}
		else
		{
			foreach ( $this->eventListeners as $eventListener )
			{
				if ( !($eventListener instanceof ListensToIceHawkEvents) )
				{
					throw new InvalidEventListenerCollection();
				}
			}
		}
	}

	/**
	 * @param ServesIceHawkEventData $event
	 */
	private function publishEvent( ServesIceHawkEventData $event )
	{
		foreach ( $this->eventListeners as $listener )
		{
			if ( $listener->acceptsEvent( $event ) )
			{
				$listener->notify( $event );
			}
		}
	}

	public function handleRequest()
	{
		try
		{
			$this->redirectOrHandleRequest();
		}
		catch ( \Exception $uncaughtException )
		{
			$this->delegate->handleUncaughtException( $uncaughtException );
		}
	}

	/**
	 * @throws \Exception
	 */
	private function redirectOrHandleRequest()
	{
		$redirect = $this->getRedirect();

		if ( $redirect->urlEquals( $this->requestInfo->getUri() ) )
		{
			$uriComponents = $this->getUriComponents();
			$request       = $this->getRequest( $uriComponents );

			$handlingRequestEvent = new HandlingRequestEvent( $this->requestInfo, $request );
			$this->publishEvent( $handlingRequestEvent );

			$requestHandler = $this->getDomainRequestHandler( $uriComponents, $request );
			$requestHandler->handleRequest();

			$requestWasHandledEvent = new RequestWasHandledEvent( $this->requestInfo, $request );
			$this->publishEvent( $requestWasHandledEvent );
		}
		else
		{
			$redirect->respond();
		}
	}

	/**
	 * @return Responses\Redirect
	 */
	private function getRedirect()
	{
		return $this->uriRewriter->rewrite( $this->requestInfo );
	}

	/**
	 * @throws MalformedRequestUri
	 * @return ServesUriComponents
	 */
	private function getUriComponents()
	{
		return $this->uriResolver->resolveUri( $this->requestInfo );
	}

	/**
	 * @param ServesUriComponents $uriComponents
	 *
	 * @throws Exceptions\InvalidRequestMethod
	 * @return Interfaces\ServesGetRequestData|Interfaces\ServesPostRequestData
	 */
	private function getRequest( ServesUriComponents $uriComponents )
	{
		$requestBuilder = new RequestBuilder( $this->requestInfo, $uriComponents );

		return $requestBuilder->buildRequest( $_GET, $_POST, $_FILES );
	}

	/**
	 * @param ServesUriComponents $uriComponents
	 * @param ServesRequestData   $request
	 *
	 * @return HandlesDomainRequests
	 */
	private function getDomainRequestHandler( ServesUriComponents $uriComponents, ServesRequestData $request )
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			$this->projectNamespace,
			$this->requestInfo->getMethod(),
			$uriComponents
		);

		return $domainRequestHandlerBuilder->buildDomainRequestHandler( $request );
	}
}
