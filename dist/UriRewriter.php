<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

/**
 * Class UriRewriter
 *
 * @package Fortuneglobe\IceHawk
 */
class UriRewriter implements RewritesUri
{
	/**
	 * @param ServesRequestInfo $request_info
	 *
	 * @return string
	 */
	public function rewrite( ServesRequestInfo $request_info )
	{
		return $request_info->getUri();
	}
}