<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Builders\RequestBuilder;
use Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestHandlerConfig;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;
use Fortuneglobe\IceHawk\Responses\BadRequest;
use Fortuneglobe\IceHawk\Responses\NotFound;

/**
 * Class RequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
final class RequestHandler
{

	/** @var ServesRequestInfo */
	private $requestInfo;

	/** @var ServesRequestHandlerConfig */
	private $configDelegate;

	/**
	 * @param ServesRequestInfo          $requestInfo
	 * @param ServesRequestHandlerConfig $configDelegate
	 *
	 * @throws MalformedRequestUri
	 */
	public function __construct(
		ServesRequestInfo $requestInfo,
		ServesRequestHandlerConfig $configDelegate
	)
	{
		$this->requestInfo    = $requestInfo;
		$this->configDelegate = $configDelegate;
	}

	public function handle()
	{
		try
		{
			$this->redirectIfNeeded();

			$domainRequestHandler = $this->getDomainRequestHandler();

			$domainRequestHandler->handleRequest();
		}
		catch ( MalformedRequestUri $e )
		{
			( new NotFound() )->respond();
		}
		catch ( BuildingDomainRequestHandlerFailed $e )
		{
			( new NotFound() )->respond();
		}
		catch ( InvalidRequestType $e )
		{
			( new BadRequest( [ $e->getMessage() ] ) )->respond();
		}
		catch ( \Exception $e )
		{
			( new BadRequest( [ $e->getMessage() ] ) )->respond();
		}
	}

	private function redirectIfNeeded()
	{
		$uriRewriter = $this->configDelegate->getUriRewriter();
		$redirect    = $uriRewriter->rewrite( $this->requestInfo );

		if ( !$redirect->urlEquals( $this->requestInfo->getUri() ) )
		{
			$redirect->respond();
			exit();
		}
	}

	/**
	 * @throws BuildingDomainRequestHandlerFailed
	 * @throws InvalidRequestType
	 * @return HandlesDomainRequests
	 */
	private function getDomainRequestHandler()
	{
		$uriComponents               = $this->getUriComponents();
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			$this->configDelegate->getProjectNamespace(),
			$this->configDelegate->getRequestMethod()
		);

		$request = $this->getRequest( $this->requestInfo, $uriComponents );

		return $domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $request );
	}

	/**
	 * @throws MalformedRequestUri
	 * @return ServesUriComponents
	 */
	private function getUriComponents()
	{
		$uriResolver = $this->configDelegate->getUriResolver();

		return $uriResolver->resolveUri( $this->requestInfo );
	}

	/**
	 * @param ServesRequestInfo   $requestInfo
	 * @param ServesUriComponents $uriComponents
	 *
	 * @throws Exceptions\InvalidRequestMethod
	 * @return Interfaces\ServesGetRequestData|Interfaces\ServesPostRequestData
	 */
	private function getRequest( ServesRequestInfo $requestInfo, ServesUriComponents $uriComponents )
	{
		$requestBuilder = new RequestBuilder( $requestInfo, $uriComponents );

		return $requestBuilder->buildRequest( $_GET, $_POST, $_FILES );
	}
}
