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
	 * @param ServesRequestInfo $requestInfo
	 *
	 * @return Redirect
	 */
	public function rewrite( ServesRequestInfo $requestInfo );
}