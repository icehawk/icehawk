<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Builders;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed;
use Fortuneglobe\IceHawk\Exceptions\MissingInterfaceImplementationForHandlingDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\BuildsDomainRequestHandlers;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesUriComponents;

/**
 * Class DomainRequestHandlerBuilder
 *
 * @package Fortuneglobe\IceHawk\Builders
 */
final class DomainRequestHandlerBuilder implements BuildsDomainRequestHandlers
{

	/** @var string */
	private $domainNamespace;

	/** @var string */
	private $requestMethod;

	/** @var ServesUriComponents */
	private $uriComponents;

	/**
	 * @param string $domainNamespace
	 * @param string              $requestMethod
	 * @param ServesUriComponents $uriComponents
	 */
	public function __construct( $domainNamespace, $requestMethod, ServesUriComponents $uriComponents )
	{
		$this->domainNamespace = $domainNamespace;
		$this->requestMethod   = $requestMethod;
		$this->uriComponents   = $uriComponents;
	}

	/**
	 * @param ServesRequestData $request
	 *
	 * @throws MissingInterfaceImplementationForHandlingDomainRequests
	 * @throws BuildingDomainRequestHandlerFailed
	 * @return HandlesDomainRequests
	 */
	public function buildDomainRequestHandler( ServesRequestData $request )
	{
		$domainName              = $this->getStringToCamelCase( $this->uriComponents->getDomain() );
		$demandName              = $this->getStringToCamelCase( $this->uriComponents->getDemand() );
		$subNamespaceReadOrWrite = $this->getSubNamespaceReadOrWrite();

		$className = sprintf(
			"%s\\%s\\%s\\%sRequestHandler",
			$this->domainNamespace,
			$domainName,
			$subNamespaceReadOrWrite,
			$demandName
		);

		if ( class_exists( $className, true ) )
		{
			$handlerInstance = new $className( $request );
			$this->guardInstanceImplementsHandlerInterface( $handlerInstance );

			return $handlerInstance;
		}
		else
		{
			throw new BuildingDomainRequestHandlerFailed( $className );
		}
	}

	/**
	 * @param string $string
	 *
	 * @return string
	 */
	private function getStringToCamelCase( $string )
	{
		$words = preg_split( "#[^a-z0-9]#i", $string );
		$words = array_map( 'ucfirst', $words );

		return join( '', $words );
	}

	/**
	 * @return string
	 */
	private function getSubNamespaceReadOrWrite()
	{
		if ( $this->requestMethod == Http::METHOD_POST )
		{
			return 'Write';
		}
		else
		{
			return 'Read';
		}
	}

	/**
	 * @param object $instance
	 *
	 * @throws MissingInterfaceImplementationForHandlingDomainRequests
	 */
	private function guardInstanceImplementsHandlerInterface( $instance )
	{
		if ( !($instance instanceof HandlesDomainRequests) )
		{
			throw new MissingInterfaceImplementationForHandlingDomainRequests();
		}
	}
}
