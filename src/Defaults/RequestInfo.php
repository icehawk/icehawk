<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Class RequestInfo
 * @package IceHawk\IceHawk
 */
final class RequestInfo implements ProvidesRequestInfo
{
	/** @var array */
	private $serverData = [];

	/**
	 * @param array $serverData
	 */
	public function __construct( array $serverData )
	{
		$this->serverData = $serverData;
	}

	public static function fromEnv() : self
	{
		return new self( $_SERVER );
	}

	public function isSecure() : bool
	{
		return ($this->get( 'HTTPS' ) == 'on');
	}

	/**
	 * @param string $key
	 *
	 * @return null|string
	 */
	private function get( string $key )
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

	public function getMethod() : string
	{
		$method = $this->get( 'REQUEST_METHOD' );

		if ( null !== $method )
		{
			return strtoupper( $method );
		}
		else
		{
			return '';
		}
	}

	public function getUri() : string
	{
		$uri = $this->get( 'REQUEST_URI' );

		if ( null !== $uri )
		{
			return preg_replace( ['#\/+#', '#\?.*$#'], ['/', ''], $uri );
		}
		else
		{
			return '/';
		}
	}

	public function getHost() : string
	{
		return $this->get( 'HTTP_HOST' ) ? : '';
	}

	public function getUserAgent() : string
	{
		return $this->get( 'HTTP_USER_AGENT' ) ? : '';
	}

	public function getServerAddress() : string
	{
		return $this->get( 'SERVER_ADDR' ) ? : '';
	}

	public function getClientAddress() : string
	{
		return $this->get( 'REMOTE_ADDR' ) ? : '';
	}

	public function getRequestTimeFloat() : float
	{
		return floatval( $this->get( 'REQUEST_TIME_FLOAT' ) );
	}

	public function getAcceptedContentTypes() : string
	{
		return $this->get( 'HTTP_ACCEPT' ) ? : '';
	}

	public function getQueryString() : string
	{
		return $this->get( 'QUERY_STRING' ) ? : '';
	}

	public function getReferer() : string
	{
		return $this->get( 'HTTP_REFERER' ) ? : '';
	}

	public function getAuthUser() : string
	{
		return $this->get( 'PHP_AUTH_USER' ) ? : '';
	}

	public function getAuthPassword() : string
	{
		return $this->get( 'PHP_AUTH_PW' ) ? : '';
	}

	public function getContentType() : string
	{
		return $this->get( 'CONTENT_TYPE' ) ? : '';
	}

	public function getContentLength() : string
	{
		return $this->get( 'CONTENT_LENGTH' ) ? : '';
	}
}
