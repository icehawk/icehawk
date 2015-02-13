<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Builders;

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
	private $projectNamespace;

	/**
	 * @param string $projectNamespace
	 */
	public function __construct( $projectNamespace )
	{
		$this->projectNamespace = $projectNamespace;
	}

	/**
	 * @param ServesUriComponents $uriComponents
	 * @param ServesRequestData   $request
	 *
	 * @throws MissingInterfaceImplementationForHandlingDomainRequests
	 * @throws BuildingDomainRequestHandlerFailed
	 * @return HandlesDomainRequests
	 */
	public function buildDomainRequestHandler( ServesUriComponents $uriComponents, ServesRequestData $request )
	{
		$domainName = $this->getStringToCamelCase( $uriComponents->getDomain() );
		$demandName = $this->getStringToCamelCase( $uriComponents->getDemand() );

		$className = sprintf( "%s\\%s\\%sRequestHandler", $this->projectNamespace, $domainName, $demandName );

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