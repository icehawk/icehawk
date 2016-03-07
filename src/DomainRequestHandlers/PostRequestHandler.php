<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\DomainRequestHandlers;

use Fortuneglobe\IceHawk\DomainRequestHandler;
use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\HandlesPostRequest;
use Fortuneglobe\IceHawk\Interfaces\ServesPostRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;

/**
 * Class PostRequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class PostRequestHandler extends DomainRequestHandler implements HandlesPostRequest
{
	/**
	 * @param ServesRequestData $request
	 *
	 * @throws InvalidRequestType
	 */
	final protected function guardValidRequestType( ServesRequestData $request )
	{
		if ( !($request instanceof ServesPostRequestData) )
		{
			throw new InvalidRequestType( "Expected post request, got " . get_class( $this->request ) );
		}
	}

	final public function handleRequest()
	{
		/** @var ServesPostRequestData $request */
		$request = $this->request;

		$this->handle( $request );
	}
}
