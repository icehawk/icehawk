<?php
/**
 *
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class RequestInfo
 *
 * @package Fortuneglobe\IceHawk
 */
final class RequestInfo implements ServesRequestInfo
{

	/** @var array */
	private $serverData = [ ];

	/**
	 * @param array $serverData
	 */
	public function __construct( array $serverData )
	{
		$this->serverData = $serverData;
	}

	/**
	 * @return RequestInfo
	 */
	public static function fromEnv()
	{
		return new self( $_SERVER );
	}

	/**
	 * @return string|null
	 */
	public function getMethod()
	{
		return strtoupper( $this->get( 'REQUEST_METHOD' ) );
	}

	/**
	 * @return string|null
	 */
	public function getUri()
	{
		$uri = $this->get( 'REQUEST_URI' );

		return preg_replace( [ '#\/+#', '#\?.*$#' ], [ '/', '' ], $uri );
	}

	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->get( 'HTTP_HOST' );
	}

	/**
	 * @return string
	 */
	public function getUserAgent()
	{
		return $this->get( 'USER_AGENT' );
	}

	/**
	 * @return string
	 */
	public function getServerAddress()
	{
		return $this->get( 'SERVER_ADDR' );
	}

	/**
	 * @return string
	 */
	public function getClientAddress()
	{
		return $this->get( 'REMOTE_ADDR' );
	}

	/**
	 * @return float
	 */
	public function getRequestTimeFloat()
	{
		return floatval( $this->get( 'REQUEST_TIME_FLOAT' ) );
	}

	/**
	 * @return string
	 */
	public function acceptsContentTypes()
	{
		return $this->get( 'HTTP_ACCEPT' );
	}

	/**
	 * @return string
	 */
	public function getQueryString()
	{
		return $this->get( 'QUERY_STRING' );
	}

	/**
	 * @param string $key
	 *
	 * @return null|string
	 */
	private function get( $key )
	{
		if ( isset($this->serverData[ $key ]) )
		{
			return $this->serverData[ $key ];
		}
		else
		{
			return null;
		}
	}
}
