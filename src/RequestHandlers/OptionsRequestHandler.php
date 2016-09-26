<?php
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
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

		(new Options( $allowedRequestMethods ))->respond();
	}

	private function getOptions() : array
	{
		$handlerRoutes = $this->getHandlerRoutes();

		$requestMethods = [];
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
		$requestMethods = [];

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
