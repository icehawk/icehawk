<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Config\ConfigGuard;
use Fortuneglobe\IceHawk\Config\ConfigWrapper;
use Fortuneglobe\IceHawk\Constants\HttpMethod;
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
use Fortuneglobe\IceHawk\Exceptions\RequestMethodNotAllowed;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\ProvidesHandlerDemand;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\PubSub\Interfaces\CarriesEventData;
use Fortuneglobe\IceHawk\RequestHandlers\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\RequestHandlers\RequestHandlerBuilder;
use Fortuneglobe\IceHawk\Requests\RequestBuilder;
use Fortuneglobe\IceHawk\Responses\MethodNotAllowed;
use Fortuneglobe\IceHawk\Responses\Options;
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
	private $setUpDelegate;

	/**
	 * @param ConfiguresIceHawk $config
	 * @param SetsUpEnvironment $setUpDelegate
	 */
	public function __construct( ConfiguresIceHawk $config, SetsUpEnvironment $setUpDelegate )
	{
		$this->config        = $config;
		$this->setUpDelegate = $setUpDelegate;
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
		$this->setUpDelegate->setUpErrorHandling();
		$this->setUpDelegate->setUpSessionHandling();
		$this->setUpDelegate->setUpGlobalVars();

		$this->config = new ConfigWrapper( $this->config );

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
		$configGuard = new ConfigGuard( $this->config );
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

	/**
	 * @throws \Throwable
	 */
	public function handleRequest()
	{
		$requestInfo = $this->config->getRequestInfo();

		try
		{
			$this->guardRequestMethodIsAllowed();

			if ( $requestInfo->getMethod() == HttpMethod::OPTIONS )
			{
				( new Options( $this->config->getAllowedRequestMethods() ) )->respond();
			}
			else
			{
				$this->redirectOrHandleRequest();
			}
		}
		catch ( RequestMethodNotAllowed $e )
		{
			( new MethodNotAllowed() )->respond();
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
	 * @throws RequestMethodNotAllowed
	 */
	private function guardRequestMethodIsAllowed()
	{
		$requestMethod         = $this->config->getRequestInfo()->getMethod();
		$allowedRequestMethods = $this->config->getAllowedRequestMethods();

		if ( !in_array( $requestMethod, $allowedRequestMethods ) )
		{
			throw ( new RequestMethodNotAllowed() )->withRequestMethod( $requestMethod );
		}
	}

	private function handleByMethod()
	{
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
			$handlerDemand  = $this->getHandlerDemand();
			$request        = $this->getRequest( $handlerDemand );
			$requestHandler = $this->getRequestHandler( $handlerDemand, $request );

			$handlingEvent = new HandlingRequestEvent( $request );
			$this->publishEvent( $handlingEvent );

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
	 * @return ProvidesHandlerDemand
	 */
	private function getHandlerDemand() : ProvidesHandlerDemand
	{
		$uriResolver = $this->config->getUriResolver();
		$requestInfo = $this->config->getRequestInfo();

		$handlerDemand = $uriResolver->resolveUri( $requestInfo );

		return $handlerDemand;
	}

	/**
	 * @param ProvidesHandlerDemand $handlerDemand
	 *
	 * @throws Exceptions\InvalidRequestMethod
	 * @return ProvidesReadRequestData|ProvidesWriteRequestData|ProvidesRequestData
	 */
	private function getRequest( ProvidesHandlerDemand $handlerDemand ) : ProvidesRequestData
	{
		$requestInfo = $this->config->getRequestInfo();
		$builder     = new RequestBuilder( $requestInfo, $handlerDemand );

		$request = $builder->build( $_GET, $_POST, $_FILES );

		return $request;
	}

	/**
	 * @param ProvidesHandlerDemand $handlerDemand
	 * @param ProvidesRequestData   $request


*
*@throws MissingInterfaceImplementationForHandlingDomainRequests
	 * @throws BuildingDomainRequestHandlerFailed
	 * @return HandlesRequest
	 */
	private function getRequestHandler(
		ProvidesHandlerDemand $handlerDemand,
		ProvidesRequestData $request
	) : HandlesRequest
	{
		$domainNamespace = $this->config->getHandlerRootNamespace();
		$requestInfo     = $this->config->getRequestInfo();

		$builder = new RequestHandlerBuilder(
			$domainNamespace,
			$requestInfo->getMethod(),
			$handlerDemand
		);

		$domainRequestHandler = $builder->build( $request );

		return $domainRequestHandler;
	}
}
