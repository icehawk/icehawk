<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Builders\DomainRequestHandlerBuilder;
use Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Exceptions\MalformedRequestUri;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;

/**
 * Class RequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
final class RequestHandler
{

	/** @var ServesRequestInfo */
	private $requestInfo;

	/** @var ServesUriComponents */
	private $uriComponents;

	/** @var string */
	private $projectNamespace;

	/**
	 * @param ServesRequestInfo   $requestInfo
	 * @param ServesUriComponents $uriComponents
	 * @param string              $projectNamespace
	 *
	 * @throws MalformedRequestUri
	 */
	public function __construct(
		ServesRequestInfo $requestInfo,
		ServesUriComponents $uriComponents,
		$projectNamespace
	)
	{
		$this->requestInfo      = $requestInfo;
		$this->uriComponents    = $uriComponents;
		$this->projectNamespace = $projectNamespace;
	}

	/**
	 * @param ServesRequestData $request
	 */
	public function handle( ServesRequestData $request )
	{
		$domainRequestHandler = $this->buildDomainRequestHandler( $request );

		$domainRequestHandler->handleRequest();
	}

	/**
	 * @param ServesRequestData $request
	 *
	 * @throws BuildingDomainRequestHandlerFailed
	 * @throws InvalidRequestType
	 * @return HandlesDomainRequests
	 */
	private function buildDomainRequestHandler( ServesRequestData $request )
	{
		$domainRequestHandlerBuilder = new DomainRequestHandlerBuilder(
			$this->projectNamespace,
			$this->requestInfo->getMethod(),
			$this->uriComponents
		);

		return $domainRequestHandlerBuilder->buildDomainRequestHandler( $request );
	}
}
