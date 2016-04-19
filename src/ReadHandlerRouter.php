<?php
namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\HandlesReadRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToReadHandler;

/**
 * Class ReadHandlerRouter
 *
 * @package Fortuneglobe\IceHawk
 */
class ReadHandlerRouter implements RoutesToReadHandler
{
	/** @var HandlesReadRequest */
	private $requestHandler;

	/** @var array */
	private $uriParams;

	public function __construct( HandlesReadRequest $requestHandler, array $uriParams = [ ] )
	{
		$this->requestHandler = $requestHandler;
		$this->uriParams      = $uriParams;
	}

	public function getUriParams() : array
	{
		return $this->uriParams;
	}

	public function getRequestHandler() : HandlesReadRequest
	{
		return $this->requestHandler;
	}
}