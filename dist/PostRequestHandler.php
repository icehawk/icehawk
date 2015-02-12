<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;

/**
 * Class PostRequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class PostRequestHandler extends DomainRequestHandler implements HandlesPostRequest
{
	/**
	 * @throws InvalidRequestType
	 */
	protected function guardValidRequestHandlerType()
	{
		if ( !($this->request instanceof ServesPostRequestData) )
		{
			throw new InvalidRequestType( "Expected post request, got " . get_class( $this->request ) );
		}
	}

	public function handleRequest()
	{
		/** @var ServesPostRequestData $request */
		$request = $this->request;

		$this->handle( $request );
	}
}