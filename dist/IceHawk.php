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
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\HandlesIceHawkTasks;
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

	/** @var ServesIceHawkConfig */
	private $config;

	/** @var HandlesIceHawkTasks */
	private $delegate;

	/**
	 * @param ServesIceHawkConfig $config
	 * @param HandlesIceHawkTasks $delegate
	 */
	public function __construct( ServesIceHawkConfig $config, HandlesIceHawkTasks $delegate )
	{
		$this->config   = $config;
		$this->delegate = $delegate;
	}

	public function init()
	{
		$this->delegate->configureErrorHandling();
		$this->delegate->configureSession();

		$initializedEvent = new IceHawkWasInitializedEvent();
		$this->publishEvent( $initializedEvent );
	}

	/**
	 * @param ServesIceHawkEventData $event
	 */
	private function publishEvent( ServesIceHawkEventData $event )
	{
		foreach ( $this->config->getEventListeners() as $listener )
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
		$requestInfo = $this->config->getRequestInfo();

		$uriRewriter = $this->config->getUriRewriter();
		$redirect    = $uriRewriter->rewrite( $requestInfo );

		if ( $redirect->urlEquals( $requestInfo->getUri() ) )
		{
			$uriComponents = $this->getUriComponents( $requestInfo );
			$request       = $this->getRequest( $requestInfo, $uriComponents );

			$handlingRequestEvent = new HandlingRequestEvent( $requestInfo, $request );
			$this->publishEvent( $handlingRequestEvent );

			$requestHandler = $this->getDomainRequestHandler( $requestInfo, $uriComponents, $request );
			$requestHandler->handleRequest();

			$requestWasHandledEvent = new RequestWasHandledEvent( $requestInfo, $request );
			$this->publishEvent( $requestWasHandledEvent );
		}
		else
		{
			$redirect->respond();
		}
	}

	/**
	 * @param ServesRequestInfo $requestInfo
	 *
	 * @throws MalformedRequestUri
	 * @return ServesUriComponents
	 */
	private function getUriComponents( ServesRequestInfo $requestInfo )
	{
		$uriResolver = $this->config->getUriResolver();

		return $uriResolver->resolveUri( $requestInfo );
	}

	/**
	 * @param ServesRequestInfo   $requestInfo
	 * @param ServesUriComponents $uriComponents
	 *
	 * @throws Exceptions\InvalidRequestMethod
	 * @return Interfaces\ServesGetRequestData|Interfaces\ServesPostRequestData
	 */
	private function getRequest( ServesRequestInfo $requestInfo, ServesUriComponents $uriComponents )
	{
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		return $requestBuilder->buildRequest( $_GET, $_POST, $_FILES );
	}

	/**
	 * @param ServesRequestInfo   $requestInfo
	 * @param ServesUriComponents $uriComponents
	 * @param ServesRequestData   $request
	 *
	 * @return HandlesDomainRequests
	 */
	private function getDomainRequestHandler(
		ServesRequestInfo $requestInfo,
		ServesUriComponents $uriComponents,
		ServesRequestData $request
	)
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			$this->config->getProjectNamespace(),
			$requestInfo->getMethod(),
			$uriComponents
		);

		return $domainRequestHandlerBuilder->buildDomainRequestHandler( $request );
	}
}
