<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Constants\HttpMethod;

/**
 * Class RequestMethod
 *
 * @package Fortuneglobe\IceHawk
 */
final class RequestMethod
{
	/** @var string */
	private $method;

	/**
	 * @param string $method
	 */
	public function __construct( string $method )
	{
		$this->guardMethodIsValid( $method );

		$this->method = $method;
	}

	/**
	 * @param string $method
	 *
	 * @throws \Exception
	 */
	private function guardMethodIsValid( string $method )
	{
		if ( !in_array( $method, HttpMethod::ALL_METHODS ) )
		{
			throw new \Exception( 'Invalid request method' );
		}
	}

	public function toString()
	{
		return $this->method;
	}

	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * @param string $method
	 *
	 * @return RequestMethod
	 */
	public static function fromString( string $method )
	{
		return new self( $method );
	}
}