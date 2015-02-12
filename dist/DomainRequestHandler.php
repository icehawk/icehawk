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
		$this->request = $request;
		$this->guardValidRequestHandlerType();
	}

	/**
	 * @throws InvalidRequestType
	 */
	abstract protected function guardValidRequestHandlerType();
}