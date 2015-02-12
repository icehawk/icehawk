<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed;
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
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

	/** @var ServesRequestData */
	private $request;

	/**
	 * @param ServesRequestInfo          $requestInfo
	 * @param ServesRequestHandlerConfig $configDelegate
	 * @param ServesRequestData          $request
	 *
	 * @throws MalformedRequestUri
	 */
	public function __construct(
		ServesRequestInfo $requestInfo,
		ServesRequestHandlerConfig $configDelegate,
		ServesRequestData $request
	)
	{
		$this->requestInfo    = $requestInfo;
		$this->configDelegate = $configDelegate;
		$this->request        = $request;
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
		}
	}

	/**
	 * @throws BuildingDomainRequestHandlerFailed
	 * @return HandlesDomainRequests
	 */
	private function getDomainRequestHandler()
	{
		$uriComponents               = $this->getUriComponents();
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder( $this->configDelegate->getProjectNamespace() );

		return $domainRequestHandlerBuilder->buildDomainRequestHandler( $uriComponents, $this->request );
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
}
