<?php
/**
 * GET request wrapper
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;

/**
 * Class GetRequest
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
final class GetRequest implements ServesGetRequestData
{

	/** @var array */
	private $getData = [ ];

	/**
	 * @param array $getData
	 */
	public function __construct( array $getData )
	{
		$this->getData = $getData;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->getData;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( $key )
	{
		if ( isset($this->getData[ $key ]) )
		{
			return $this->getData[ $key ];
		}
		else
		{
			return null;
		}
	}
}