<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesRequestInfo
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesRequestInfo
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
}
