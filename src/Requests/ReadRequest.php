<?php
/**
 * GET request wrapper
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class GetRequest
 * @package Fortuneglobe\IceHawk\Requests
 */
final class ReadRequest implements ProvidesReadRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var array */
	private $getData;

	/**
	 * @param ProvidesRequestInfo $requestInfo
	 * @param array               $getData
	 */
	public function __construct( ProvidesRequestInfo $requestInfo, array $getData )
	{
		$this->requestInfo = $requestInfo;
		$this->getData     = $getData;
	}

	/**
	 * @return array
	 */
	public function getData() : array
	{
		return $this->getData;
	}

	/**
	 * @param string $key
	 *
	 * @return null|string|array
	 */
	public function get( string $key )
	{
		return $this->getData[ $key ] ?? null;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}