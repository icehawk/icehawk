<?php declare(strict_types=1);
/**
 * Copyright (c) 2017 Holger Woltersdorf & Contributors
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
	private $serverData;

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

	public function newWithOverwrites( array $array ) : ProvidesRequestInfo
	{
		return new self( array_merge( $this->serverData, $array ) );
	}

	public function isSecure() : bool
	{
		$httpsValue = (string)$this->get( 'HTTPS' );

		return (strcasecmp( $httpsValue, 'on' ) === 0);
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	private function get( string $key )
	{
		return $this->serverData[ $key ] ?? null;
	}

	public function getArgv() : array
	{
		return (array)$this->get( 'argv' );
	}

	public function getArgc() : int
	{
		return (int)$this->get( 'argc' );
	}

	public function getMethod() : string
	{
		$method = (string)$this->get( 'REQUEST_METHOD' );

		return strtoupper( $method );
	}

	public function getUri() : string
	{
		$uri = (string)$this->get( 'REQUEST_URI' );

		if ( '' !== $uri )
		{
			return (string)preg_replace( ['#\/+#', '#\?.*$#'], ['/', ''], $uri );
		}

		return '/';
	}

	public function getHost() : string
	{
		return (string)$this->get( 'HTTP_HOST' );
	}

	public function getConnection() : string
	{
		return (string)$this->get( 'HTTP_CONNECTION' );
	}

	public function getUserAgent() : string
	{
		return (string)$this->get( 'HTTP_USER_AGENT' );
	}

	public function getServerAddress() : string
	{
		return (string)$this->get( 'SERVER_ADDR' );
	}

	public function getClientAddress() : string
	{
		return (string)$this->get( 'REMOTE_ADDR' );
	}

	public function getClientHost() : string
	{
		return (string)$this->get( 'REMOTE_HOST' );
	}

	public function getClientPort() : string
	{
		return (string)$this->get( 'REMOTE_PORT' );
	}

	public function getClientUser() : string
	{
		return (string)$this->get( 'REMOTE_USER' );
	}

	public function getRedirectClientUser() : string
	{
		return (string)$this->get( 'REDIRECT_REMOTE_USER' );
	}

	public function getRequestTimeFloat() : float
	{
		return (float)$this->get( 'REQUEST_TIME_FLOAT' );
	}

	public function getRequestTime() : string
	{
		return (string)$this->get( 'REQUEST_TIME' );
	}

	public function getAcceptedContentTypes() : string
	{
		return (string)$this->get( 'HTTP_ACCEPT' );
	}

	public function getAcceptedCharsets() : string
	{
		return (string)$this->get( 'HTTP_ACCEPT_CHARSET' );
	}

	public function getAcceptedEncoding() : string
	{
		return (string)$this->get( 'HTTP_ACCEPT_ENCODING' );
	}

	public function getAcceptedLanguage() : string
	{
		return (string)$this->get( 'HTTP_ACCEPT_LANGUAGE' );
	}

	public function getQueryString() : string
	{
		return (string)$this->get( 'QUERY_STRING' );
	}

	public function getReferer() : string
	{
		return (string)$this->get( 'HTTP_REFERER' );
	}

	public function getAuthType() : string
	{
		return (string)$this->get( 'AUTH_TYPE' );
	}

	public function getAuthDigest() : string
	{
		return (string)$this->get( 'PHP_AUTH_DIGEST' );
	}

	public function getAuthUser() : string
	{
		return (string)$this->get( 'PHP_AUTH_USER' );
	}

	public function getAuthPassword() : string
	{
		return (string)$this->get( 'PHP_AUTH_PW' );
	}

	public function getContentType() : string
	{
		return (string)$this->get( 'CONTENT_TYPE' );
	}

	public function getContentLength() : string
	{
		return (string)$this->get( 'CONTENT_LENGTH' );
	}

	public function getPhpSelf() : string
	{
		return (string)$this->get( 'PHP_SELF' );
	}

	public function getGatewayInterface() : string
	{
		return (string)$this->get( 'GATEWAY_INTERFACE' );
	}

	public function getServerName() : string
	{
		return (string)$this->get( 'SERVER_NAME' );
	}

	public function getServerSoftware() : string
	{
		return (string)$this->get( 'SERVER_SOFTWARE' );
	}

	public function getServerProtocol() : string
	{
		return (string)$this->get( 'SERVER_PROTOCOL' );
	}

	public function getServerAdmin() : string
	{
		return (string)$this->get( 'SERVER_ADMIN' );
	}

	public function getServerPort() : string
	{
		return (string)$this->get( 'SERVER_PORT' );
	}

	public function getServerSignature() : string
	{
		return (string)$this->get( 'SERVER_SIGNATURE' );
	}

	public function getPathTranslated() : string
	{
		return (string)$this->get( 'PATH_TRANSLATED' );
	}

	public function getDocumentRoot() : string
	{
		return (string)$this->get( 'DOCUMENT_ROOT' );
	}

	public function getScriptName() : string
	{
		return (string)$this->get( 'SCRIPT_NAME' );
	}

	public function getScriptFilename() : string
	{
		return (string)$this->get( 'SCRIPT_FILENAME' );
	}

	public function getPathInfo() : string
	{
		return (string)$this->get( 'PATH_INFO' );
	}

	public function getOriginalPathInfo() : string
	{
		return (string)$this->get( 'ORIG_PATH_INFO' );
	}

	public function getCustomValue( string $key ) : string
	{
		return (string)$this->get( $key );
	}
}
