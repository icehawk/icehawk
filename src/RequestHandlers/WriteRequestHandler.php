<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Constants\HandlerMethodInterfaceMap;
use Fortuneglobe\IceHawk\Events\HandlingWriteRequestEvent;
use Fortuneglobe\IceHawk\Events\WriteRequestWasHandledEvent;
use Fortuneglobe\IceHawk\Exceptions\RequestMethodNotAllowed;
use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\Mappers\UploadedFilesMapper;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequestInput;
use Fortuneglobe\IceHawk\Responses\MethodNotAllowed;

/**
 * Class WriteRequestHandler
 *
 * @package Fortuneglobe\IceHawk\RequestHandlers
 */
final class WriteRequestHandler extends AbstractRequestHandler
{
	public function handleRequest()
	{
		try
		{
			$response = $this->resolveAndHandleRequest();
			$response->respond();
		}
		catch ( RequestMethodNotAllowed $e )
		{
			( new MethodNotAllowed( $e->getRequestMethod() ) )->respond();
		}
		catch ( \Throwable $throwable )
		{
			$finalResponder = $this->config->getFinalWriteRequestResponder();
			$finalResponder->handleUncaughtException( $throwable, $this->getRequest( [ ] ) );
		}
	}

	private function resolveAndHandleRequest() : ServesResponse
	{
		$requestInfo  = $this->config->getRequestInfo();
		$handlerRoute = $this->getHandlerRoute();

		$this->guardHandlerAcceptsRequestMethod( $handlerRoute->getRequestHandler(), $requestInfo->getMethod() );

		$request        = $this->getRequest( $handlerRoute->getUriParams() );
		$requestHandler = $handlerRoute->getRequestHandler();

		$handlingEvent = new HandlingWriteRequestEvent( $request );
		$this->publishEvent( $handlingEvent );

		$response = $requestHandler->handle( $request );

		$handledEvent = new WriteRequestWasHandledEvent( $request );
		$this->publishEvent( $handledEvent );
		
		return $response;
	}

	private function getHandlerRoute() : RoutesToWriteHandler
	{
		$uriResolver = $this->config->getWriteRequestResolver();
		$requestInfo = $this->config->getRequestInfo();

		$handlerRoute = $uriResolver->resolve( $requestInfo );

		return $handlerRoute;
	}

	private function guardHandlerAcceptsRequestMethod( HandlesWriteRequest $handler, string $requestMethod )
	{
		$requiredInterface = HandlerMethodInterfaceMap::HTTP_METHODS[ $requestMethod ];

		if ( !($handler instanceof $requiredInterface) )
		{
			throw ( new RequestMethodNotAllowed() )->withRequestMethod( $requestMethod );
		}
	}

	private function getRequest( array $uriParams ) : ProvidesWriteRequestData
	{
		$requestInfo   = $this->config->getRequestInfo();
		$parserFactory = $this->config->getBodyParserFactory();

		$body = $this->getRequestBody();

		$bodyParser = $parserFactory->selectParserByContentType( $requestInfo->getContentType() );
		$bodyParams = $bodyParser->parse( $body );

		$requestData   = array_merge( $_POST, $bodyParams, $uriParams );
		$uploadedFiles = $this->getUploadedFiles();

		$requestInput = new WriteRequestInput( $body, $requestData, $uploadedFiles );

		return new WriteRequest( $requestInfo, $requestInput );
	}

	private function getRequestBody() : string
	{
		$body = file_get_contents( 'php://input' );

		return $body ? : '';
	}

	private function getUploadedFiles() : array
	{
		return ( new UploadedFilesMapper( $_FILES ) )->mapToInfoObjects();
	}
}