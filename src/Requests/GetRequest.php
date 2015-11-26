<?php
/**
 * GET request wrapper
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ServesGetRequestData;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class GetRequest
 *
 * @package Fortuneglobe\IceHawk\Requests
 */
final class GetRequest implements ServesGetRequestData
{
	/** @var ServesRequestInfo */
	private $requestInfo;

	/** @var array */
	private $getData = [ ];

	/**
	 * @param ServesRequestInfo $requestInfo
	 * @param array             $getData
	 */
	public function __construct( ServesRequestInfo $requestInfo, array $getData )
	{
		$this->requestInfo = $requestInfo;
		$this->getData     = $getData;
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

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo()
	{
		return $this->requestInfo;
	}
}