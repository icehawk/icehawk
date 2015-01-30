<?php
/**
 * GET request wrapper
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestData;

/**
 * Class GetRequest
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
final class GetRequest implements ServesRequestData
{

	/** @var array */
	private $get_data = [ ];

	/**
	 * @param array $get_data
	 */
	public function __construct( array $get_data )
	{
		$this->get_data = $get_data;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->get_data;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( $key )
	{
		if ( isset($this->get_data[ $key ]) )
		{
			return $this->get_data[ $key ];
		}
		else
		{
			return null;
		}
	}
}