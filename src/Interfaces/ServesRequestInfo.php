<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesRequestInfo
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesRequestInfo
{
	/**
	 * @return bool
	 */
	public function isSecure();

	/**
	 * @return string
	 */
	public function getMethod();

	/**
	 * @return string
	 */
	public function getUri();

	/**
	 * @return string
	 */
	public function getHost();

	/**
	 * @return string
	 */
	public function getUserAgent();

	/**
	 * @return string
	 */
	public function getServerAddress();

	/**
	 * @return string
	 */
	public function getClientAddress();

	/**
	 * @return float
	 */
	public function getRequestTimeFloat();

	/**
	 * @return string
	 */
	public function getAcceptedContentTypes();

	/**
	 * @return string
	 */
	public function getQueryString();

	/**
	 * @return string|null
	 */
	public function getAuthUser();

	/**
	 * @return string|null
	 */
	public function getAuthPassword();
}
