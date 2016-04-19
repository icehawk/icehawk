<?php
namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\HandlesWriteRequest;
use Fortuneglobe\IceHawk\Interfaces\RoutesToWriteHandler;

/**
 * Class WriterHandlerRoute
 *
 * @package Fortuneglobe\IceHawk
 */
class WriteHandlerRouter implements RoutesToWriteHandler
{
	/** @var HandlesWriteRequest */
	private $requestHandler;

	/** @var array */
	private $uriParams;

	public function __construct( HandlesWriteRequest $requestHandler, array $uriParams = [ ] )
	{
		$this->requestHandler = $requestHandler;
		$this->uriParams      = $uriParams;
	}
	
	public function getUriParams() : array
	{
		return $this->uriParams;
	}

	public function getRequestHandler() : HandlesWriteRequest
	{
		return $this->requestHandler;
	}
}