<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Constants\HandlerMethodInterfaceMap;
use Fortuneglobe\IceHawk\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\Responses\Options;
use Fortuneglobe\IceHawk\Routing\OptionsRouter;

/**
 * Class OptionsRequestHandler
 *
 * @package Fortuneglobe\IceHawk\RequestHandlers
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

		return $router->findMatchingRoutes( $requestInfo );
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