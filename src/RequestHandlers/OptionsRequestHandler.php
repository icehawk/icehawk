<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Constants\HandlerMethodInterfaceMap;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Responses\Options;

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

	private function getReadHandlerRoute() : RoutesToReadHandler
	{
		$uriResolver = $this->config->getReadRequestResolver();
		$requestInfo = $this->config->getRequestInfo();

		$handlerRoute = $uriResolver->resolve( $requestInfo );

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

	private function getWriteHandlerRoute() : RoutesToWriteHandler
	{
		$uriResolver = $this->config->getReadRequestResolver();
		$requestInfo = $this->config->getRequestInfo();

		$handlerRoute = $uriResolver->resolve( $requestInfo );

		return $handlerRoute;
	}
}