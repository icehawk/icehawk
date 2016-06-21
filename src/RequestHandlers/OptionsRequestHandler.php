<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Constants\HandlerMethodInterfaceMap;
use Fortuneglobe\IceHawk\Constants\HttpMethod;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Responses\Options;
use Fortuneglobe\IceHawk\Routing\ReadRouter;
use Fortuneglobe\IceHawk\Routing\WriteRouter;

/**
 * Class OptionsRequestHandler
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
			$handlerRoute = $this->getReadHandlerRoute();
			$handler      = $handlerRoute->getRequestHandler();

			return $this->getImplementedRequestMethods( $handler );
		}
		catch ( UnresolvedRequest $e )
		{
			return [ ];
		}
	}

	/**
	 * @throws UnresolvedRequest
	 */
	private function getReadHandlerRoute() : RoutesToReadHandler
	{
		$readRoutes  = $this->config->getReadRoutes();
		$requestInfo = $this->config->getRequestInfo();
		$readRouter  = new ReadRouter( $readRoutes );

		$handlerRoutes = [];
		foreach( HttpMethod::READ_METHODS as $readMethod )
		{
			$requestInfo = new RequestInfo(
				['REQUEST_URI' => $requestInfo->getUri(), 'REQUEST_METHOD' => $requestInfo->getMethod() ]
			);
			$handlerRoutes[] = $readRouter->findMatchingRoute( $requestInfo );
		}

		$handlerRoute = $readRouter->findMatchingRoute( $requestInfo );

		return $handlerRoute;
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
			$handlerRoute = $this->getWriteHandlerRoute();
			$handler      = $handlerRoute->getRequestHandler();

			return $this->getImplementedRequestMethods( $handler );
		}
		catch ( UnresolvedRequest $e )
		{
			return [ ];
		}
	}

	/**
	 * @throws UnresolvedRequest
	 */
	private function getWriteHandlerRoute() : RoutesToWriteHandler
	{
		$writeRoutes = $this->config->getWriteRoutes();
		$requestInfo = $this->config->getRequestInfo();
		$writeRouter = new WriteRouter( $writeRoutes );

		$handlerRoute = $writeRouter->findMatchingRoute( $requestInfo );

		return $handlerRoute;
	}
}