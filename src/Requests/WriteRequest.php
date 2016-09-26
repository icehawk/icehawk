<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Requests;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestData;
use IceHawk\IceHawk\Interfaces\ProvidesWriteRequestInputData;

/**
 * Class PostRequest
 * @package IceHawk\IceHawk\Requests
 */
final class WriteRequest implements ProvidesWriteRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var ProvidesWriteRequestInputData */
	private $inputData;

	public function __construct( ProvidesRequestInfo $requestInfo, ProvidesWriteRequestInputData $inputData )
	{
		$this->requestInfo = $requestInfo;
		$this->inputData   = $inputData;
	}

	public function getInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getInput() : ProvidesWriteRequestInputData
	{
		return $this->inputData;
	}
}
