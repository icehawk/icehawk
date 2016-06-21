<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Constants\HandlerMethodInterfaceMap;
use Fortuneglobe\IceHawk\Constants\HttpMethod;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\Responses\Options;
use Fortuneglobe\IceHawk\Routing\ReadRouter;
use Fortuneglobe\IceHawk\Routing\RouteRequest;
use Fortuneglobe\IceHawk\Routing\WriteRouter;

/**
 * Class OptionsRequestHandler
 *
 * @package Fortuneglobe\IceHawk\RequestHandlers
 */
final class OptionsRequestHandler extends AbstractRequestHandler
{
	public function handleRequest()
	{
		$allowedRequestMethods = array_merge( $this->getReadOptions(), $this->getWriteOptions() );

		( new Options( $allowedRequestMethods ) )->respond();
	}

	private function getReadOptions() : array
	{
		try
		{
			$handlerRoutes = $this->getReadHandlerRoutes();

			$requestMethods = [ ];
			foreach ( $handlerRoutes as $handlerRoute )
			{
				$handler          = $handlerRoute->getRequestHandler();
				$requestMethods[] = $this->getImplementedRequestMethods( $handler );
			}

			return $requestMethods;
		}
		catch ( UnresolvedRequest $e )
		{
			return [ ];
		}
	}

	/**
	 * @throws UnresolvedRequest
	 */
	private function getReadHandlerRoutes() : array
	{
		$readRoutes  = $this->config->getReadRoutes();
		$requestInfo = $this->config->getRequestInfo();
		$readRouter  = new ReadRouter( $readRoutes );

		$handlerRoutes = [ ];
		foreach ( HttpMethod::READ_METHODS as $readMethod )
		{
			$routeRequest    = new RouteRequest( $requestInfo->getUri(), $readMethod );
			$handlerRoutes[] = $readRouter->findMatchingRoute( $routeRequest );
		}

		return $handlerRoutes;
	}

	private function getImplementedRequestMethods( HandlesRequest $handler ) : array
	{
		$requestMethods = [ ];

		foreach ( HandlerMethodInterfaceMap::HTTP_METHODS as $requestMethod => $interface )
		{
			if ( $handler instanceof $interface )
			{
				$requestMethods[] = $requestMethod;
			}
		}

		return $requestMethods;
	}

	private function getWriteOptions() : array
	{
		try
		{
			$handlerRoutes = $this->getWriteHandlerRoutes();

			$requestMethods = [ ];
			foreach ( $handlerRoutes as $handlerRoute )
			{
				$handler          = $handlerRoute->getRequestHandler();
				$requestMethods[] = $this->getImplementedRequestMethods( $handler );
			}

			return $requestMethods;
		}
		catch ( UnresolvedRequest $e )
		{
			return [ ];
		}
	}

	/**
	 * @throws UnresolvedRequest
	 */
	private function getWriteHandlerRoutes() : array
	{
		$writeRoutes = $this->config->getWriteRoutes();
		$requestInfo = $this->config->getRequestInfo();
		$writeRouter = new WriteRouter( $writeRoutes );

		$handlerRoutes = [ ];
		foreach ( HttpMethod::READ_METHODS as $readMethod )
		{
			$routeRequest    = new RouteRequest( $requestInfo->getUri(), $readMethod );
			$handlerRoutes[] = $writeRouter->findMatchingRoute( $routeRequest );
		}

		return $handlerRoutes;
	}
}