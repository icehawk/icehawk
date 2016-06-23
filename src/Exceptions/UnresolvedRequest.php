<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Exceptions;

use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;

/**
 * Class UnresolvedRequest
 * @package Fortuneglobe\IceHawk\Exceptions
 */
final class UnresolvedRequest extends IceHawkException
{
	/** @var ProvidesDestinationInfo */
	private $destinationInfo = '';

	public function withDestinationInfo( ProvidesDestinationInfo $destinationInfo ) : self
	{
		$this->destinationInfo = $destinationInfo;

		return $this;
	}

	public function getDestinationInfo() : ProvidesDestinationInfo
	{
		return $this->destinationInfo;
	}
}