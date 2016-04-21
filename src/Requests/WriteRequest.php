<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestInputData;

/**
 * Class PostRequest
 * @package Fortuneglobe\IceHawk\Requests
 */
final class WriteRequest implements ProvidesWriteRequestData
{
	/** @var ProvidesRequestInfo */
	private $requestInfo;

	/** @var ProvidesWriteRequestInputData */
	private $inputData;

	public function __construct( ProvidesRequestInfo $requestInfo, ProvidesWriteRequestInputData $inputData )
	{
		$this->requestInfo   = $requestInfo;
		$this->inputData     = $inputData;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}

	public function getInputData() : ProvidesWriteRequestInputData
	{
		return $this->inputData;
	}
}
