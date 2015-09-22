<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesIceHawkConfig
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesIceHawkConfig
{
	/**
	 * @return string
	 */
	public function getDomainNamespace();

	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter();

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver();

	/**
	 * @return array|ListensToEvents[]
	 */
	public function getEventListeners();

	/**
	 * @return ServesRequestInfo
	 */
	public function getRequestInfo();
}
