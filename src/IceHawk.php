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
use Fortuneglobe\IceHawk\Exceptions\InvalidDomainNamespace;
use Fortuneglobe\IceHawk\Exceptions\InvalidEventListenerCollection;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestInfoImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriResolverImplementation;
use Fortuneglobe\IceHawk\Exceptions\InvalidUriRewriterImplementation;
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\ControlsHandlingBehaviour;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesEventData;
use Fortuneglobe\IceHawk\Interfaces\ServesIceHawkConfig;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;

/**
 * Class IceHawk
 *
 * @package Fortuneglobe\IceHawk
 */
final class IceHawk
{
	/** @var ServesIceHawkConfig */
	private $config;

	/** @var ControlsHandlingBehaviour */
	private $delegate;

	/**
	 * @param ServesIceHawkConfig       $config
	 * @param ControlsHandlingBehaviour $delegate
	 */
	public function __construct( ServesIceHawkConfig $config, ControlsHandlingBehaviour $delegate )
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
		$this->delegate->setUpEnvironment();

		$this->config = new IceHawkConfigWrapper( $this->config );

		$this->guardConfigIsValid();

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

	/**
	 * @param ServesEventData $event
	 */
	private function publishEvent( ServesEventData $event )
	{
		$eventListeners = $this->config->getEventListeners();

		foreach ( $eventListeners as $listener )
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
			$redirect->respond();
		}
	}

	/**
	 * @return Responses\Redirect
	 */
	private function getRedirect()
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
	private function getUriComponents()
	{
		$uriResolver = $this->config->getUriResolver();
		$requestInfo = $this->config->getRequestInfo();

		$uriComponents = $uriResolver->resolveUri( $requestInfo );

		return $uriComponents;
	}

	/**
	 * @param ServesUriComponents $uriComponents
	 *
	 * @throws Exceptions\InvalidRequestMethod
	 * @return Interfaces\ServesGetRequestData|Interfaces\ServesPostRequestData
	 */
	private function getRequest( ServesUriComponents $uriComponents )
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
	 * @return HandlesDomainRequests
	 */
	private function getDomainRequestHandler( ServesUriComponents $uriComponents, ServesRequestData $request )
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
