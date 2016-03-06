<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Builders\RequestBuilder;
use Fortuneglobe\IceHawk\Events\HandlingRequestEvent;
use Fortuneglobe\IceHawk\Events\IceHawkWasInitializedEvent;
use Fortuneglobe\IceHawk\Events\RedirectingEvent;
use Fortuneglobe\IceHawk\Events\RequestWasHandledEvent;
use Fortuneglobe\IceHawk\Events\UncaughtExceptionWasThrownEvent;
use Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed;
use Fortuneglobe\IceHawk\Exceptions\InvalidDomainNamespace;
use Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation;
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Exceptions\MissingInterfaceImplementationForHandlingDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class IceHawk
 * @package Fortuneglobe\IceHawk
 */
final class IceHawk
{
	/** @var ConfiguresIceHawk */
	private $config;

	/** @var SetsUpEnvironment */
	private $delegate;

	/**
	 * @param ConfiguresIceHawk $config
	 * @param SetsUpEnvironment $delegate
	 */
	public function __construct( ConfiguresIceHawk $config, SetsUpEnvironment $delegate )
	{
		$this->config   = $config;
		$this->delegate = $delegate;
	}

	/**
	 * @throws InvalidEventListenerCollection
	 * @throws InvalidDomainNamespace
	 * @throws InvalidRequestInfoImplementation
	 * @throws InvalidUriResolverImplementation
	 * @throws InvalidUriRewriterImplementation
	 */
	public function init()
	{
		$this->delegate->setUpErrorHandling();
		$this->delegate->setUpSessionHandling();
		$this->delegate->setUpGlobalVars();

		$this->config = new IceHawkConfigWrapper( $this->config );

		$this->guardConfigIsValid();

		$this->registerEventSubscribers();

		$requestInfo      = $this->config->getRequestInfo();
		$initializedEvent = new IceHawkWasInitializedEvent( $requestInfo );
		$this->publishEvent( $initializedEvent );
	}

	/**
	 * @throws InvalidEventListenerCollection
	 * @throws InvalidDomainNamespace
	 * @throws InvalidRequestInfoImplementation
	 * @throws InvalidUriResolverImplementation
	 * @throws InvalidUriRewriterImplementation
	 */
	private function guardConfigIsValid()
	{
		$configGuard = new IceHawkConfigGuard( $this->config );
		$configGuard->guardConfigIsValid();
	}

	private function registerEventSubscribers()
	{
		$eventPublisher = EventPublisher::singleton();

		foreach ( $this->config->getEventSubscribers() as $subscriber )
		{
			$eventPublisher->register( $subscriber );
		}
	}

	private function publishEvent( CarriesEventData $event ) : bool
	{
		return EventPublisher::singleton()->publish( $event );
	}

	public function handleRequest()
	{
		try
		{
			$this->redirectOrHandleRequest();
		}
		catch ( \Throwable $throwable )
		{
			$event     = new UncaughtExceptionWasThrownEvent( $throwable, $this->config->getRequestInfo() );
			$published = $this->publishEvent( $event );

			if ( !$published )
			{
				throw $throwable;
			}
		}
	}

	/**
	 * @throws MissingInterfaceImplementationForHandlingDomainRequests
	 * @throws BuildingDomainRequestHandlerFailed
	 * @throws MalformedRequestUri
	 */
	private function redirectOrHandleRequest()
	{
		$redirect    = $this->getRedirect();
		$requestInfo = $this->config->getRequestInfo();

		if ( $redirect->urlEquals( $requestInfo->getUri() ) )
		{
			$uriComponents = $this->getUriComponents();
			$request       = $this->getRequest( $uriComponents );

			$handlingEvent = new HandlingRequestEvent( $request );
			$this->publishEvent( $handlingEvent );

			$requestHandler = $this->getDomainRequestHandler( $uriComponents, $request );
			$requestHandler->handleRequest();

			$handledEvent = new RequestWasHandledEvent( $request );
			$this->publishEvent( $handledEvent );
		}
		else
		{
			$redirectingEvent = new RedirectingEvent( $redirect, $requestInfo );
			$this->publishEvent( $redirectingEvent );

			$redirect->respond();
		}
	}

	/**
	 * @return Redirect
	 */
	private function getRedirect() : Redirect
	{
		$uriRewriter = $this->config->getUriRewriter();
		$requestInfo = $this->config->getRequestInfo();

		$redirect = $uriRewriter->rewrite( $requestInfo );

		return $redirect;
	}

	/**
	 * @throws MalformedRequestUri
	 * @return ServesUriComponents
	 */
	private function getUriComponents() : ServesUriComponents
	{
		$uriResolver = $this->config->getUriResolver();
		$requestInfo = $this->config->getRequestInfo();

		$uriComponents = $uriResolver->resolveUri( $requestInfo );

		return $uriComponents;
	}

	/**
	 * @param ServesUriComponents $uriComponents

	 *
*@throws Exceptions\InvalidRequestMethod
	 * @return ServesGetRequestData|ServesPostRequestData|ServesRequestData
	 */
	private function getRequest( ServesUriComponents $uriComponents ) : ServesRequestData
	{
		$requestInfo = $this->config->getRequestInfo();
		$builder     = new RequestBuilder( $requestInfo, $uriComponents );

		$request = $builder->buildRequest( $_GET, $_POST, $_FILES );

		return $request;
	}

	/**
	 * @param ServesUriComponents $uriComponents
	 * @param ServesRequestData   $request
	 *
	 * @throws MissingInterfaceImplementationForHandlingDomainRequests
	 * @throws BuildingDomainRequestHandlerFailed
	 * @return HandlesDomainRequests
	 */
	private function getDomainRequestHandler(
		ServesUriComponents $uriComponents, ServesRequestData $request
	) : HandlesDomainRequests
	{
		$domainNamespace = $this->config->getDomainNamespace();
		$requestInfo     = $this->config->getRequestInfo();

		$builder = new DomainRequestHandlerBuilder(
			$domainNamespace,
			$requestInfo->getMethod(),
			$uriComponents
		);

		$domainRequestHandler = $builder->buildDomainRequestHandler( $request );

		return $domainRequestHandler;
	}
}
