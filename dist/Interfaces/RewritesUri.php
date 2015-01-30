<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RewritesUri
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RewritesUri
{
	/**
	 * @param ServesRequestInfo $request_info
	 *
	 * @return string
	 */
	public function rewrite( ServesRequestInfo $request_info );
}