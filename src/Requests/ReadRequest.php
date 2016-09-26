<?php
/**
 * GET request wrapper
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesReadRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesReadRequestInputData;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class GetRequest
 * @package IceHawk\IceHawk\Requests
 */
final class ReadRequest implements ProvidesReadRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var ProvidesReadRequestInputData */
	private $inputData;

	public function __construct( ProvidesRequestInfo $requestInfo, ProvidesReadRequestInputData $inputData )
	{
		$this->requestInfo = $requestInfo;
		$this->inputData   = $inputData;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getInputData() : ProvidesReadRequestInputData
	{
		return $this->inputData;
	}
}
