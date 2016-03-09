<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Demands;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class Query
 * @package Fortuneglobe\IceHawk\Demands
 */
abstract class Query
{

	/** @var ProvidesReadRequestData */
	protected $request;

	/**
	 * @param ProvidesReadRequestData $request
	 */
	public function __construct( ProvidesReadRequestData $request )
	{
		$this->request = $request;
	}

	/**
	 * @param string $key
	 *
	 * @return array|null|string
	 */
	protected function getRequestValue( string $key )
	{
		return $this->request->get( $key );
	}

	/**
	 * @return array
	 */
	protected function getRequestData() : array
	{
		return $this->request->getData();
	}

	/**
	 * @return ProvidesRequestInfo
	 */
	final public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->request->getRequestInfo();
	}
}
