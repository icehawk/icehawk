<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Exceptions;

use Fortuneglobe\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class UnresolvedRequest
 * @package Fortuneglobe\IceHawk\Exceptions
 */
final class UnresolvedRequest extends IceHawkException
{
	/** @var ProvidesRequestInfo */
	private $requestInfo = '';

	public function withRequestInfo( ProvidesRequestInfo $requestInfo ) : self
	{
		$this->requestInfo = $requestInfo;

		return $this;
	}

	public function getRequestInfo() : ProvidesRequestInfo
	{
		return $this->requestInfo;
	}
}