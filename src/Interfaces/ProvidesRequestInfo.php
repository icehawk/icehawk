<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesRequestInfo
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesRequestInfo
{
	public function isSecure() : bool;

	public function getMethod() : string;

	public function getUri() : string;

	public function getHost() : string;

	public function getUserAgent() : string;

	public function getServerAddress() : string;

	public function getClientAddress() : string;

	public function getRequestTimeFloat() : float;

	public function getAcceptedContentTypes() : string;

	public function getQueryString() : string;

	public function getReferer() : string;

	public function getAuthUser() : string;

	public function getAuthPassword() : string;

	public function getContentType() : string;

	public function getContentLength() : string;
}
