<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToReadHandler;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesMatchResult;

/**
 * Class ReadRoute
 * @package Fortuneglobe\IceHawk\Routing
 */
final class ReadRoute implements RoutesToReadHandler
{
	/** @var ProvidesMatchResult */
	private $pattern;

	/** @var HandlesReadRequest */
	private $requestHandler;

	public function __construct( ProvidesMatchResult $pattern, HandlesReadRequest $requestHandler )
	{
		$this->pattern        = $pattern;
		$this->requestHandler = $requestHandler;
	}

	public function matches( ProvidesDestinationInfo $destinationInfo ) : bool
	{
		return $this->pattern->matches( $destinationInfo->getUri() );
	}

	public function getUriParams() : array
	{
		return $this->pattern->getMatches();
	}

	public function getRequestHandler() : HandlesReadRequest
	{
		return $this->requestHandler;
	}
}