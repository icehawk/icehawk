<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Constants\HttpMethod;
use Fortuneglobe\IceHawk\Exceptions\BuildingDomainRequestHandlerFailed;
use Fortuneglobe\IceHawk\Exceptions\MissingInterfaceImplementationForHandlingDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\BuildsRequestHandler;
use Fortuneglobe\IceHawk\Interfaces\ProvidesHandlerDemand;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestData;
use Fortuneglobe\IceHawk\RequestHandlers\Interfaces\HandlesRequest;

/**
 * Class DomainRequestHandlerBuilder
 * @package Fortuneglobe\IceHawk\RequestHandlers
 */
final class RequestHandlerBuilder implements BuildsRequestHandler
{

	/** @var string */
	private $domainNamespace;

	/** @var string */
	private $requestMethod;

	/** @var ProvidesHandlerDemand */
	private $handlerDemand;

	/**
	 * @param string                $domainNamespace
	 * @param string                $requestMethod
	 * @param ProvidesHandlerDemand $handlerDemand
	 */
	public function __construct( $domainNamespace, $requestMethod, ProvidesHandlerDemand $handlerDemand )
	{
		$this->domainNamespace = $domainNamespace;
		$this->requestMethod   = $requestMethod;
		$this->handlerDemand   = $handlerDemand;
	}

	/**
	 * @param ProvidesRequestData $request
	 *
*@throws MissingInterfaceImplementationForHandlingDomainRequests
	 * @throws BuildingDomainRequestHandlerFailed
	 * @return HandlesRequest
	 */
	public function build( ProvidesRequestData $request ) : HandlesRequest
	{
		$domainName   = $this->getStringToCamelCase( $this->handlerDemand->getDomain() );
		$demandName   = $this->getStringToCamelCase( $this->handlerDemand->getDemand() );
		$subNamespace = $this->getSubNamespaceReadOrWrite();

		$className = sprintf(
			"%s\\%s\\%s\\%sRequestHandler",
			$this->domainNamespace,
			$domainName,
			$subNamespace,
			$demandName
		);

		if ( class_exists( $className, true ) )
		{
			$class           = new \ReflectionClass( $className );
			$handlerInstance = $class->newInstance( $request );

			$this->guardInstanceImplementsHandlerInterface( $handlerInstance );

			return $handlerInstance;
		}
		else
		{
			throw new BuildingDomainRequestHandlerFailed( $className );
		}
	}

	private function getStringToCamelCase( string $string ) : string
	{
		$words = preg_split( "#[^a-z0-9]#i", $string );
		$words = array_map( 'ucfirst', $words );

		return join( '', $words );
	}

	private function getSubNamespaceReadOrWrite() : string
	{
		if ( in_array( $this->requestMethod, HttpMethod::WRITE_METHODS ) )
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
		if ( !($instance instanceof HandlesRequest) )
		{
			throw new MissingInterfaceImplementationForHandlingDomainRequests();
		}
	}
}
