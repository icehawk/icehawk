<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Constants\HandlerInterfaceMap;
use Fortuneglobe\IceHawk\Events\HandlingReadRequestEvent;
use Fortuneglobe\IceHawk\Events\ReadRequestWasHandledEvent;
use Fortuneglobe\IceHawk\Events\RedirectingEvent;
use Fortuneglobe\IceHawk\Exceptions\RequestMethodNotAllowed;
use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\Requests\ReadRequest;
use Fortuneglobe\IceHawk\Responses\MethodNotAllowed;
use Fortuneglobe\IceHawk\Responses\Redirect;

/**
 * Class ReadRequestHandler
 * @package Fortuneglobe\IceHawk\RequestHandlers
 */
final class ReadRequestHandler extends AbstractRequestHandler
{
	public function handleRequest()
	{
		try
		{
			$this->redirectOrHandleRequest();
		}
		catch ( RequestMethodNotAllowed $e )
		{
			( new MethodNotAllowed( $e->getRequestMethod() ) )->respond();
		}
		catch ( \Throwable $throwable )
		{
			$finalResponder = $this->config->getFinalReadRequestResponder();
			$finalResponder->handleUncaughtException( $throwable, $this->getRequest( [ ] ) );
		}
		finally
		{
			if ( !headers_sent() )
			{
				$finalResponder = $this->config->getFinalReadRequestResponder();
				$finalResponder->handleNoResponse( $this->getRequest( [ ] ) );
			}
		}
	}

	private function redirectOrHandleRequest()
	{
		$redirect    = $this->getRedirect();
		$requestInfo = $this->config->getRequestInfo();

		if ( $redirect->urlEquals( $requestInfo->getUri() ) )
		{
			$this->resolveAndHandleRequest();
		}
		else
		{
			$redirectingEvent = new RedirectingEvent( $redirect, $requestInfo );
			$this->publishEvent( $redirectingEvent );

			$redirect->respond();
		}
	}

	/**
	 * @return Redirect
	 */
	private function getRedirect() : Redirect
	{
		$uriRewriter = $this->config->getUriRewriter();
		$requestInfo = $this->config->getRequestInfo();

		return $uriRewriter->rewrite( $requestInfo );
	}

	/**
	 * @throws RequestMethodNotAllowed
	 */
	private function resolveAndHandleRequest()
	{
		$requestInfo  = $this->config->getRequestInfo();
		$handlerRoute = $this->getHandlerRoute();

		$this->guardHandlerAcceptsRequestMethod( $handlerRoute->getRequestHandler(), $requestInfo->getMethod() );

		$request        = $this->getRequest( $handlerRoute->getUriParams() );
		$requestHandler = $handlerRoute->getRequestHandler();

		$handlingEvent = new HandlingReadRequestEvent( $request );
		$this->publishEvent( $handlingEvent );

		$requestHandler->handle( $request );

		$handledEvent = new ReadRequestWasHandledEvent( $request );
		$this->publishEvent( $handledEvent );
	}

	private function getHandlerRoute() : RoutesToReadHandler
	{
		$uriResolver = $this->config->getReadRequestResolver();
		$requestInfo = $this->config->getRequestInfo();

		$handlerRoute = $uriResolver->resolve( $requestInfo );

		return $handlerRoute;
	}

	/**
	 * @param HandlesReadRequest $handler
	 * @param string             $requestMethod
	 *
	 * @throws RequestMethodNotAllowed
	 */
	private function guardHandlerAcceptsRequestMethod( HandlesReadRequest $handler, string $requestMethod )
	{
		$requiredInterface = HandlerInterfaceMap::HTTP_METHODS[ $requestMethod ];

		if ( !($handler instanceof $requiredInterface) )
		{
			throw ( new RequestMethodNotAllowed() )->withRequestMethod( $requestMethod );
		}
	}

	/**
	 * @param array $uriParams
	 *
	 * @return ProvidesReadRequestData
	 */
	private function getRequest( array $uriParams ) : ProvidesReadRequestData
	{
		$requestInfo = $this->config->getRequestInfo();
		$getData     = array_merge( $_GET, $uriParams );

		return new ReadRequest( $requestInfo, $getData );
	}
}