<?php
/**
 *
 * @author h.woltersdorf
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
	 * @return bool
	 */
	public function isSecure()
	{
		return ($this->get( 'HTTPS' ) == 'on');
	}

	/**
	 * @return string|null
	 */
	public function getMethod()
	{
		$method = $this->get( 'REQUEST_METHOD' );

		if ( !is_null( $method ) )
		{
			return strtoupper( $method );
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return string|null
	 */
	public function getUri()
	{
		$uri = $this->get( 'REQUEST_URI' );

		if ( !is_null( $uri ) )
		{
			return preg_replace( [ '#\/+#', '#\?.*$#' ], [ '/', '' ], $uri );
		}
		else
		{
			return null;
		}
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
		return $this->get( 'HTTP_USER_AGENT' );
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
		$requestTime = $this->get( 'REQUEST_TIME_FLOAT' );

		if ( !is_null( $requestTime ) )
		{
			return floatval( $requestTime );
		}
		else
		{
			return null;
		}
	}

	/**
	 * @return string
	 */
	public function getAcceptedContentTypes()
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
	 * @return null|string
	 */
	public function getReferer()
	{
		return $this->get( 'HTTP_REFERER' );
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
