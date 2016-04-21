<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Demands;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestInputData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class Query
 *
 * @package Fortuneglobe\IceHawk\Demands
 */
abstract class Query
{
	/** @var ProvidesRequestInfo */
	protected $requestInfo;

	/** @var ProvidesReadRequestInputData */
	protected $requestInput;

	/**
	 * @param ProvidesReadRequestData $request
	 */
	public function __construct( ProvidesReadRequestData $request )
	{
		$this->requestInfo  = $request->getRequestInfo();
		$this->requestInput = $request->getInputData();
	}

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( string $key )
	{
		return $this->requestInput->get( $key );
	}

	/**
	 * @return array
	 */
	protected function getRequestData() : array
	{
		return $this->requestInput->getData();
	}

	/**
	 * @return ProvidesRequestInfo
	 */
	final public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}
