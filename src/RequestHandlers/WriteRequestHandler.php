<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Events\HandlingWriteRequestEvent;
use IceHawk\IceHawk\Events\WriteRequestWasHandledEvent;
use IceHawk\IceHawk\Exceptions\UnresolvedRequest;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Mappers\UploadedFilesMapper;
use IceHawk\IceHawk\Requests\WriteRequest;
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
			$finalResponder->handleUncaughtException( $throwable, $this->getRequest( [ ] ) );
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

		return new WriteRequest( $requestInfo, $requestData, $body, $uploadedFiles );
	}

	private function getRequestBody() : string
	{
		$body = @stream_get_contents( fopen( 'php://input', 'r' ) );

		return $body ? : '';
	}

	private function getUploadedFiles() : array
	{
		return ( new UploadedFilesMapper( $_FILES ) )->mapToInfoObjects();
	}
}