<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\DomainRequestHandlers;

use Fortuneglobe\IceHawk\DomainRequestHandler;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;

/**
 * Class GetRequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class GetRequestHandler extends DomainRequestHandler implements HandlesGetRequest
{
	/**
	 * @param ServesRequestData $request
	 *
	 * @throws InvalidRequestType
	 */
	final protected function guardValidRequestType( ServesRequestData $request )
	{
		if ( !($request instanceof ServesGetRequestData) )
		{
			throw new InvalidRequestType( "Expected get request, got " . get_class( $this->request ) );
		}
	}

	final public function handleRequest()
	{
		/** @var ServesGetRequestData $request */
		$request = $this->request;

		$this->handle( $request );
	}
}
