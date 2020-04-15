<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use InvalidArgumentException;
use function strtoupper;

final class HttpMethod
{
	private const GET     = 'GET';

	private const HEAD    = 'HEAD';

	private const POST    = 'POST';

	private const PUT     = 'PUT';

	private const PATCH   = 'PATCH';

	private const DELETE  = 'DELETE';

	private const OPTIONS = 'OPTIONS';

	private const CONNECT = 'CONNECT';

	private const TRACE   = 'TRACE';

	private const ALL     = [
		self::GET,
		self::HEAD,
		self::POST,
		self::PUT,
		self::PATCH,
		self::DELETE,
		self::OPTIONS,
		self::CONNECT,
		self::TRACE,
	];

	private string $httpMethod;

	/**
	 * @param string $httpMethod
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( string $httpMethod )
	{
		$this->guardHttpMethodIsValid( $httpMethod );

		$this->httpMethod = strtoupper( $httpMethod );
	}

	/**
	 * @param string $httpMethod
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardHttpMethodIsValid( string $httpMethod ) : void
	{
		if ( !in_array( strtoupper( $httpMethod ), self::ALL, true ) )
		{
			throw new InvalidArgumentException( 'Invalid value for HttpMethod: ' . $httpMethod );
		}
	}

	/**
	 * @param string $httpMethod
	 *
	 * @return HttpMethod
	 * @throws InvalidArgumentException
	 */
	public static function newFromString( string $httpMethod ) : self
	{
		return new self( $httpMethod );
	}

	/**
	 * @return HttpMethod
	 */
	public static function get() : self
	{
		return new self( self::GET );
	}

	/**
	 * @return HttpMethod
	 */
	public static function head() : self
	{
		return new self( self::HEAD );
	}

	/**
	 * @return HttpMethod
	 */
	public static function post() : self
	{
		return new self( self::POST );
	}

	/**
	 * @return HttpMethod
	 */
	public static function put() : self
	{
		return new self( self::PUT );
	}

	/**
	 * @return HttpMethod
	 */
	public static function patch() : self
	{
		return new self( self::PATCH );
	}

	/**
	 * @return HttpMethod
	 */
	public static function delete() : self
	{
		return new self( self::DELETE );
	}

	/**
	 * @return HttpMethod
	 */
	public static function options() : self
	{
		return new self( self::OPTIONS );
	}

	/**
	 * @return HttpMethod
	 */
	public static function connect() : self
	{
		return new self( self::CONNECT );
	}

	/**
	 * @return HttpMethod
	 */
	public static function trace() : self
	{
		return new self( self::TRACE );
	}

	public function toString() : string
	{
		return $this->httpMethod;
	}

	public function equals( HttpMethod $other, HttpMethod ...$others ) : bool
	{
		if ( $other->httpMethod === $this->httpMethod )
		{
			return true;
		}

		foreach ( $others as $otherLoop )
		{
			if ( $otherLoop->httpMethod === $this->httpMethod )
			{
				return true;
			}
		}

		return false;
	}
}
