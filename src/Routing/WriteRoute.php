<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;
use Fortuneglobe\IceHawk\Routing\Interfaces\RoutesToWriteHandler;
use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesMatchResult;

/**
 * Class WriteRoute
 * @package Fortuneglobe\IceHawk\Routing
 */
final class WriteRoute implements RoutesToWriteHandler
{
	/** @var ProvidesMatchResult */
	private $pattern;

	/** @var HandlesWriteRequest */
	private $requestHandler;

	public function __construct( ProvidesMatchResult $pattern, HandlesWriteRequest $requestHandler )
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

	public function getRequestHandler() : HandlesWriteRequest
	{
		return $this->requestHandler;
	}
}