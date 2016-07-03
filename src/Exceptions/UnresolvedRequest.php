<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Exceptions;

use IceHawk\IceHawk\Routing\Interfaces\ProvidesDestinationInfo;

/**
 * Class UnresolvedRequest
 * @package IceHawk\IceHawk\Exceptions
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