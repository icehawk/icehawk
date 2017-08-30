<?php declare(strict_types=1);
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

use IceHawk\IceHawk\Events\HandlingReadRequestEvent;
use IceHawk\IceHawk\Events\ReadRequestWasHandledEvent;
use IceHawk\IceHawk\Exceptions\UnresolvedRequest;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Requests\ReadRequest;
use IceHawk\IceHawk\Requests\ReadRequestInput;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToReadHandler;
use IceHawk\IceHawk\Routing\ReadRouter;
use IceHawk\IceHawk\Routing\RouteRequest;

/**
 * Class ReadRequestHandler
 * @package IceHawk\IceHawk\RequestHandlers
 */
final class ReadRequestHandler extends AbstractRequestHandler
{
	public function handleRequest()
	{
		try
		{
			$this->resolveAndHandleRequest();
		}
		catch ( \Throwable $throwable )
		{
			$finalResponder = $this->config->getFinalReadResponder();
			$finalResponder->handleUncaughtException( $throwable, $this->getRequest( [] ) );
		}
	}

	private function resolveAndHandleRequest()
	{
		$handlerRoute = $this->getHandlerRoute();

		$request        = $this->getRequest( $handlerRoute->getUriParams() );
		$requestHandler = $handlerRoute->getRequestHandler();

		$handlingEvent = new HandlingReadRequestEvent( $request );
		$this->publishEvent( $handlingEvent );

		$requestHandler->handle( $request );

		$handledEvent = new ReadRequestWasHandledEvent( $request );
		$this->publishEvent( $handledEvent );
	}

	/**
	 * @throws UnresolvedRequest
	 * @throws \IceHawk\IceHawk\Routing\Exceptions\RoutesAreNotTraversable
	 */
	private function getHandlerRoute() : RoutesToReadHandler
	{
		$readRoutes   = $this->config->getReadRoutes();
		$routeRequest = new RouteRequest( $this->requestInfo->getUri(), $this->requestInfo->getMethod() );
		$readRouter   = new ReadRouter( $readRoutes );

		return $readRouter->findMatchingRoute( $routeRequest );
	}

	/**
	 * @param array $uriParams
	 *
	 * @return ProvidesReadRequestData
	 */
	private function getRequest( array $uriParams ) : ProvidesReadRequestData
	{
		$cookies = $this->config->getCookies();

		$requestData  = array_merge( $_GET, $uriParams );
		$requestInput = new ReadRequestInput( $requestData );

		return new ReadRequest( $this->requestInfo, $cookies, $requestInput );
	}
}
