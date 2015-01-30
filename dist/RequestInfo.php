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
	private $server_data = [ ];

	/**
	 * @param array $server_data
	 */
	public function __construct( array $server_data )
	{
		$this->server_data = $server_data;
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
		return $this->get( 'REQUEST_METHOD' );
	}

	/**
	 * @return string|null
	 */
	public function getUri()
	{
		$uri = $this->get( 'REQUEST_URI' );

		return preg_replace( '#\/+#', '/', $uri );
	}

	/**
	 * @return string
	 */
	public function getServerName()
	{
		return $this->get( 'SERVER_NAME' );
	}

	/**
	 * @param string $key
	 *
	 * @return null|string
	 */
	private function get( $key )
	{
		if ( isset($this->server_data[ $key ]) )
		{
			return $this->server_data[ $key ];
		}
		else
		{
			return null;
		}
	}
}
