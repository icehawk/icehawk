<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Exceptions;

/**
 * Class InvalidRequestType
 * @package Fortuneglobe\IceHawk\Exceptions
 */
final class InvalidRequestType extends IceHawkException
{
	/** @var string */
	private $requestType = '';

	public function withRequestType( string $requestType ) : self
	{
		$this->requestType = $requestType;

		return $this;
	}

	public function getRequestType() : string
	{
		return $this->requestType;
	}
}