<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\HandlesRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToHandler;

/**
 * Class UriComponents
 * @package Fortuneglobe\IceHawk
 */
final class HandlerRoute implements RoutesToHandler
{

	/** @var HandlesRequest */
	private $requestHandler;

	/** @var array */
	private $uriParams;

	public function __construct( HandlesRequest $requestHandler, array $uriParams = [ ] )
	{
		$this->requestHandler = $requestHandler;
		$this->uriParams      = $uriParams;
	}

	public function getRequestHandler() : string
	{
		return $this->requestHandler;
	}

	public function getUriParams() : array
	{
		return $this->uriParams;
	}
}