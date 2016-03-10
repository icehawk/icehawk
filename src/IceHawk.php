<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Config\ConfigGuard;
use Fortuneglobe\IceHawk\Config\ConfigWrapper;
use Fortuneglobe\IceHawk\Constants\HandlerInterfaceMap;
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
use Fortuneglobe\IceHawk\Exceptions\MissingInterfaceImplementationForHandlingDomainRequests;
use Fortuneglobe\IceHawk\Exceptions\RequestMethodNotAllowed;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ConfiguresIceHawk;
use Fortuneglobe\IceHawk\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Interfaces\SetsUpEnvironment;
use Fortuneglobe\IceHawk\PubSub\EventPublisher;
use Fortuneglobe\IceHawk\PubSub\Interfaces\PublishesEvents;
use Fortuneglobe\IceHawk\RequestHandlers\OptionsRequestHandler;
use Fortuneglobe\IceHawk\RequestHandlers\ReadRequestHandler;
use Fortuneglobe\IceHawk\RequestHandlers\WriteRequestHandler;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Responses\MethodNotAllowed;
use Fortuneglobe\IceHawk\Responses\MethodNotImplemented;
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

	/** @var PublishesEvents */
	private $eventPublisher;

	/**
	 * @param ConfiguresIceHawk $config
	 * @param SetsUpEnvironment $setUpDelegate
	 */
	public function __construct( ConfiguresIceHawk $config, SetsUpEnvironment $setUpDelegate )
	{
		$this->config         = $config;
		$this->setUpDelegate  = $setUpDelegate;
		$this->eventPublisher = new EventPublisher();
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

		$this->eventPublisher->publish( $initializedEvent );
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
		foreach ( $this->config->getEventSubscribers() as $subscriber )
		{
			$this->eventPublisher->register( $subscriber );
		}
	}

	/**
	 * @throws \Throwable
	 */
	public function handleRequest()
	{
		$requestInfo = $this->config->getRequestInfo();

		if ( in_array( $requestInfo->getMethod(), HttpMethod::WRITE_METHODS ) )
		{
			$requestHandler = new WriteRequestHandler( $this->config, $this->eventPublisher );

			$requestHandler->handleRequest();
		}
		elseif ( in_array( $requestInfo->getMethod(), HttpMethod::READ_METHODS ) )
		{
			$requestHandler = new ReadRequestHandler( $this->config, $this->eventPublisher );

			$requestHandler->handleRequest();
		}
		elseif ( $requestInfo->getMethod() == HttpMethod::OPTIONS )
		{
			$requestHandler = new OptionsRequestHandler( $this->config, $this->eventPublisher );

			$requestHandler->handleRequest();
		}
		else
		{
			( new MethodNotImplemented( $requestInfo->getMethod() ) )->respond();
		}

		try
		{
			$this->guardRequestMethodIsAllowed();

			$this->redirectOrHandleRequest();
		}
		catch ( RequestMethodNotAllowed $e )
		{
			( new MethodNotAllowed() )->respond();
		}
		catch ( \Throwable $throwable )
		{
			$event = new UncaughtExceptionWasThrownEvent( $throwable, $requestInfo );

			$this->publishEvent( $event );
		}
		finally
		{
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

	/**
	 * @throws MissingInterfaceImplementationForHandlingDomainRequests
	 * @throws BuildingDomainRequestHandlerFailed
	 * @throws UnresolvedRequest
	 */
	private function redirectOrHandleRequest()
	{
		$redirect    = $this->getRedirect();
		$requestInfo = $this->config->getRequestInfo();

		if ( $redirect->urlEquals( $requestInfo->getUri() ) )
		{
			$this->resolveAndHandleRequest();
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

		if ( in_array( $requestInfo->getMethod(), HttpMethod::READ_METHODS ) )
		{
			return $uriRewriter->rewrite( $requestInfo );
		}

		return new Redirect( $requestInfo->getUri() );
	}

	private function resolveAndHandleRequest()
	{
		$requestInfo = $this->config->getRequestInfo();

		if ( in_array( $requestInfo->getMethod(), HttpMethod::WRITE_METHODS ) )
		{
			$handlerRoute = $this->getWriteHandlerRoute();
		}
		else
		{
			$handlerRoute = $this->getReadHandlerRoute();
		}

		$this->guardHandlerAcceptsRequestMethod( $handlerRoute->getRequestHandler(), $requestInfo->getMethod() );

		$request        = $this->getRequest( $handlerRoute->getUriParams() );
		$requestHandler = $handlerRoute->getRequestHandler();

		$handlingEvent = new HandlingRequestEvent( $request );
		$this->publishEvent( $handlingEvent );

		if ( $requestInfo->getMethod() == HttpMethod::OPTIONS )
		{
			$acceptedHttpMethods = $this->getAcceptedHttpMethods( $requestHandler );

			( new Options( $acceptedHttpMethods ) )->respond();
		}
		else
		{
			$requestHandler->handle( $request );
		}

		$handledEvent = new RequestWasHandledEvent( $request );
		$this->publishEvent( $handledEvent );
	}

	private function getReadHandlerRoute() : RoutesToReadHandler
	{
		$uriResolver = $this->config->getReadUriResolver();
		$requestInfo = $this->config->getRequestInfo();

		$handlerRoute = $uriResolver->resolveReadUri( $requestInfo );

		return $handlerRoute;
	}

	private function getWriteHandlerRoute() : RoutesToWriteHandler
	{
		$uriResolver = $this->config->getReadUriResolver();
		$requestInfo = $this->config->getRequestInfo();

		$handlerRoute = $uriResolver->resolveWriteUri( $requestInfo );

		return $handlerRoute;
	}

	/**
	 * @param HandlesRequest $handler
	 * @param string         $requestMethod
	 *
	 * @throws RequestMethodNotAllowed
	 */
	private function guardHandlerAcceptsRequestMethod( HandlesRequest $handler, string $requestMethod )
	{
		$requiredInterface = HandlerInterfaceMap::HTTP_METHODS[ $requestMethod ];

		if ( !($handler instanceof $requiredInterface) )
		{
			throw ( new RequestMethodNotAllowed() )->withRequestMethod( $requestMethod );
		}
	}

	/**
	 * @param array $uriParams
	 *
	 * @throws Exceptions\InvalidRequestMethod
	 * @return ProvidesReadRequestData|ProvidesRequestData|ProvidesWriteRequestData
	 */
	private function getRequest( array $uriParams ) : ProvidesRequestData
	{
		$requestInfo = $this->config->getRequestInfo();

		if ( in_array( $requestInfo->getMethod(), HttpMethod::WRITE_METHODS ) )
		{
			$postData = array_merge( $_POST, $uriParams );

			return new WriteRequest( $requestInfo, $postData, $_FILES );
		}

		$getData = array_merge( $_GET, $uriParams );

		return new ReadRequest( $requestInfo, $getData );
	}

	/**
	 * @param HandlesRequest $requestHandler
	 *
	 * @return array
	 */
	private function getAcceptedHttpMethods( HandlesRequest $requestHandler )
	{
		$acceptedMethods = [ ];

		foreach ( HttpMethod::ALL_METHODS as $httpMethod => $handlerInterface )
		{
			if ( $requestHandler instanceof $handlerInterface )
			{
				$acceptedMethods[] = $httpMethod;
			}
		}

		return $acceptedMethods;
	}

	private function handleUncaughtException( \Throwable $throwable, string $requestMethod )
	{
		if ( in_array( $requestMethod, HttpMethod::WRITE_METHODS ) )
		{
			$this->config->getFinalResponder()->handleUncaughtWriteException( $throwable );
		}
	}
}
