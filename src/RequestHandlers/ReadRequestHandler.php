<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Events\HandlingReadRequestEvent;
use Fortuneglobe\IceHawk\Events\ReadRequestWasHandledEvent;
use Fortuneglobe\IceHawk\Exceptions\RequestMethodNotAllowed;
use Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Requests\ReadRequestInput;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\Routing\ReadRouter;
use Fortuneglobe\IceHawk\Routing\RouteRequest;

/**
 * Class ReadRequestHandler
 *
 * @package Fortuneglobe\IceHawk\RequestHandlers
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
			$finalResponder->handleUncaughtException( $throwable, $this->getRequest( [ ] ) );
		}
	}

	/**
	 * @throws RequestMethodNotAllowed
	 */
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
	 */
	private function getHandlerRoute() : RoutesToReadHandler
	{
		$readRoutes   = $this->config->getReadRoutes();
		$requestInfo  = $this->config->getRequestInfo();
		$routeRequest = new RouteRequest( $requestInfo->getUri(), $requestInfo->getMethod() );

		$readRouter   = new ReadRouter( $readRoutes );
		$handlerRoute = $readRouter->findMatchingRoute( $routeRequest );

		return $handlerRoute;
	}

	/**
	 * @param array $uriParams
	 *
	 * @return ProvidesReadRequestData
	 */
	private function getRequest( array $uriParams ) : ProvidesReadRequestData
	{
		$requestInfo = $this->config->getRequestInfo();

		$requestData  = array_merge( $_GET, $uriParams );
		$requestInput = new ReadRequestInput( $requestData );

		return new ReadRequest( $requestInfo, $requestInput );
	}
}