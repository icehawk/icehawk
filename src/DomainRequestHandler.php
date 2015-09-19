<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\HandlesDomainRequests;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;

/**
 * Class DomainRequestHandler
 *
 * @package Fortuneglobe\IceHawk
 */
abstract class DomainRequestHandler implements HandlesDomainRequests
{

	/** @var ServesRequestData */
	protected $request;

	/**
	 * @param ServesRequestData $request
	 */
	final public function __construct( ServesRequestData $request )
	{
		$this->guardValidRequestType( $request );

		$this->request = $request;
	}

	/**
	 * @param ServesRequestData $request
	 *
	 * @throws InvalidRequestType
	 */
	abstract protected function guardValidRequestType( ServesRequestData $request );
}
