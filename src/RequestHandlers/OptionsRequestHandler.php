<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Constants\HandlerMethodInterfaceMap;
use IceHawk\IceHawk\Interfaces\HandlesRequest;
use IceHawk\IceHawk\Responses\Options;
use IceHawk\IceHawk\Routing\OptionsRouter;

/**
 * Class OptionsRequestHandler
 * @package IceHawk\IceHawk\RequestHandlers
 */
final class OptionsRequestHandler extends AbstractRequestHandler
{
	public function handleRequest()
	{
		$allowedRequestMethods = $this->getOptions();

		( new Options( $allowedRequestMethods ) )->respond();
	}

	private function getOptions() : array
	{
		$handlerRoutes = $this->getHandlerRoutes();

		$requestMethods = [ ];
		foreach ( $handlerRoutes as $handlerRoute )
		{
			$handler        = $handlerRoute->getRequestHandler();
			$requestMethods = array_merge( $requestMethods, $this->getImplementedRequestMethods( $handler ) );
		}

		return $requestMethods;
	}

	private function getHandlerRoutes() : array
	{
		$readRoutes  = $this->config->getReadRoutes();
		$writeRoutes = $this->config->getWriteRoutes();
		$routes      = array_merge( $readRoutes, $writeRoutes );
		
		$requestInfo = $this->config->getRequestInfo();
		$router      = new OptionsRouter( $routes );

		return $router->findMatchingRoutes( $requestInfo->getUri() );
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
}