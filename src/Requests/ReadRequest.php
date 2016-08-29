<?php
/**
 * GET request wrapper
 *
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class GetRequest
 *
 * @package IceHawk\IceHawk\Requests
 */
final class ReadRequest implements ProvidesReadRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/**
	 * @var array
	 */
	private $data;

	public function __construct( ProvidesRequestInfo $requestInfo, array $data )
	{
		$this->requestInfo = $requestInfo;
		$this->data        = $data;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getInputData() : array
	{
		return $this->data;
	}

	/**
	 * @param string            $key
	 * @param null|string|array $default
	 *
	 * @return null|string|array
	 */
	public function get( string $key, $default = null )
	{
		return $this->data[ $key ] ?? $default;
	}
}