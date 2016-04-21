<?php
/**
 * GET request wrapper
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Requests;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestInputData;
use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class GetRequest
 * @package Fortuneglobe\IceHawk\Requests
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