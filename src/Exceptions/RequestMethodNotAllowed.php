<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Exceptions;

/**
 * Class RequestMethodNotAllowed
 * @package Fortuneglobe\IceHawk\Exceptions
 */
final class RequestMethodNotAllowed extends IceHawkException
{
	/** @var string */
	private $requestMethod = '';

	public function withRequestMethod( string $requestMethod ) : self
	{
		$this->requestMethod = $requestMethod;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRequestMethod()
	{
		return $this->requestMethod;
	}
}