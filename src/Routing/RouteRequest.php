<?php
namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;

/**
 * Class RouteRequest
 * @package IceHawk\IceHawk\Routing
 */
final class RouteRequest implements ProvidesDestinationInfo
{
	/**
	 * @var string
	 */
	private $uri;

	/**
	 * @var string
	 */
	private $requestMethod;

	/**
	 * RouteRequest constructor.
	 *
	 * @param string $uri
	 * @param string $requestMethod
	 */
	public function __construct( string $uri, string $requestMethod )
	{
		$this->uri           = $uri;
		$this->requestMethod = $requestMethod;
	}

	public function getUri() : string
	{
		return $this->uri;
	}

	public function getRequestMethod() : string
	{
		return $this->requestMethod;
	}
}