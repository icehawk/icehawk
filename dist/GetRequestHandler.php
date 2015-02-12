<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\HandlesGetRequest;
use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;

/**
 * Class GetRequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class GetRequestHandler extends DomainRequestHandler implements HandlesGetRequest
{
	/**
	 * @throws InvalidRequestType
	 */
	protected function guardValidRequestHandlerType()
	{
		if ( !($this->request instanceof ServesGetRequestData) )
		{
			throw new InvalidRequestType( "Expected get request, got " . get_class( $this->request ) );
		}
	}

	public function handleRequest()
	{
		/** @var ServesGetRequestData $request */
		$request = $this->request;

		$this->handle( $request );
	}
}