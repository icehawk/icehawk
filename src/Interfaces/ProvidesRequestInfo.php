<?php declare(strict_types = 1);
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

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesRequestInfo
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesRequestInfo
{
	public function newWithOverwrites( array $array ) : ProvidesRequestInfo;

	public function getArgv() : array;

	public function getArgc() : int;

	public function isSecure() : bool;

	public function getMethod() : string;

	public function getUri() : string;

	public function getHost() : string;

	public function getConnection() : string;

	public function getUserAgent() : string;

	public function getServerAddress() : string;

	public function getClientAddress() : string;

	public function getClientHost() : string;

	public function getClientPort() : string;

	public function getClientUser() : string;

	public function getRedirectClientUser() : string;

	public function getRequestTimeFloat() : float;

	public function getRequestTime() : string;

	public function getAcceptedContentTypes() : string;

	public function getAcceptedCharsets() : string;

	public function getAcceptedEncoding() : string;

	public function getAcceptedLanguage() : string;

	public function getQueryString() : string;

	public function getReferer() : string;

	public function getAuthType() : string;

	public function getAuthDigest() : string;

	public function getAuthUser() : string;

	public function getAuthPassword() : string;

	public function getContentType() : string;

	public function getContentLength() : string;

	public function getPhpSelf() : string;

	public function getGatewayInterface() : string;

	public function getServerName() : string;

	public function getServerSoftware() : string;

	public function getServerProtocol() : string;

	public function getServerAdmin() : string;

	public function getServerPort() : string;

	public function getServerSignature() : string;

	public function getPathTranslated() : string;

	public function getDocumentRoot() : string;

	public function getScriptName() : string;

	public function getScriptFilename() : string;

	public function getPathInfo() : string;

	public function getOriginalPathInfo() : string;

	public function getCustomValue( string $key ) : string;
}
