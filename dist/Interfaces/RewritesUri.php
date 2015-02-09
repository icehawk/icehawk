<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

use Fortuneglobe\IceHawk\Responses\Redirect;

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
	 * @return Redirect
	 */
	public function rewrite( ServesRequestInfo $request_info );
}