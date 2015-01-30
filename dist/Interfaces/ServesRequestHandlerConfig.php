<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ServesRequestHandlerConfig
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ServesRequestHandlerConfig
{
	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter();

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver();
}