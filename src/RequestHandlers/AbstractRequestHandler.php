<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestHandlers;

use Fortuneglobe\IceHawk\Exceptions\InvalidRequestType;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestData;
use Fortuneglobe\IceHawk\RequestHandlers\Interfaces\HandlesRequest;

/**
 * Class DomainRequestHandler
 * @package Fortuneglobe\IceHawk\RequestHandlers
 */
abstract class AbstractRequestHandler implements HandlesRequest
{

	/** @var ProvidesRequestData */
	protected $request;

	/**
	 * @param ProvidesRequestData $request
	 *
	 * @throws InvalidRequestType
	 */
	final public function __construct( ProvidesRequestData $request )
	{
		$this->guardValidRequestContract( $request );

		$this->request = $request;
	}

	/**
	 * @param ProvidesRequestData $request

	 *
*@throws InvalidRequestType
	 */
	private function guardValidRequestContract( ProvidesRequestData $request )
	{
		$contract = $this->getRequestContract();

		if ( !($request instanceof $contract) )
		{
			throw ( new InvalidRequestType )->withRequestType( get_class( $request ) );
		}
	}

	abstract protected function getRequestContract() : string;
}
