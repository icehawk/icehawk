<?php declare(strict_types = 1);
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

use IceHawk\IceHawk\Events\HandlingWriteRequestEvent;
use IceHawk\IceHawk\Events\WriteRequestWasHandledEvent;
use IceHawk\IceHawk\Exceptions\UnresolvedRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Mappers\UploadedFilesMapper;
use IceHawk\IceHawk\Requests\WriteRequest;
use IceHawk\IceHawk\Requests\WriteRequestInput;
use IceHawk\IceHawk\Routing\Interfaces\RoutesToWriteHandler;
use IceHawk\IceHawk\Routing\RouteRequest;
use IceHawk\IceHawk\Routing\WriteRouter;

/**
 * Class WriteRequestHandler
 * @package IceHawk\IceHawk\RequestHandlers
 */
final class WriteRequestHandler extends AbstractRequestHandler
{
	public function handleRequest()
	{
		try
		{
			$this->resolveAndHandleRequest();
		}
		catch ( \Throwable $throwable )
		{
			$finalResponder = $this->config->getFinalWriteResponder();
			$finalResponder->handleUncaughtException( $throwable, $this->getRequest( [] ) );
		}
	}

	private function resolveAndHandleRequest()
	{
		$handlerRoute = $this->getHandlerRoute();

		$request        = $this->getRequest( $handlerRoute->getUriParams() );
		$requestHandler = $handlerRoute->getRequestHandler();

		$handlingEvent = new HandlingWriteRequestEvent( $request );
		$this->publishEvent( $handlingEvent );

		$requestHandler->handle( $request );

		$handledEvent = new WriteRequestWasHandledEvent( $request );
		$this->publishEvent( $handledEvent );
	}

	/**
	 * @throws UnresolvedRequest
	 */
	private function getHandlerRoute() : RoutesToWriteHandler
	{
		$routes       = $this->config->getWriteRoutes();
		$requestInfo  = $this->config->getRequestInfo();
		$routeRequest = new RouteRequest( $requestInfo->getUri(), $requestInfo->getMethod() );

		$router       = new WriteRouter( $routes );
		$handlerRoute = $router->findMatchingRoute( $routeRequest );

		return $handlerRoute;
	}

	private function getRequest( array $uriParams ) : ProvidesWriteRequestData
	{
		$requestInfo = $this->config->getRequestInfo();

		$body          = $this->getRequestBody();
		$requestData   = array_merge( $_POST, $uriParams );
		$uploadedFiles = $this->getUploadedFiles();

		$requestInput = new WriteRequestInput( $body, $requestData, $uploadedFiles );

		return new WriteRequest( $requestInfo, $requestInput );
	}

	private function getRequestBody() : string
	{
		$body = @stream_get_contents( fopen( 'php://input', 'r' ) );

		return $body ? : '';
	}

	private function getUploadedFiles() : array
	{
		return (new UploadedFilesMapper( $_FILES ))->mapToInfoObjects();
	}
}
