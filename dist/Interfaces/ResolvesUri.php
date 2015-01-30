<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

interface ResolvesUri
{
	/**
	 * @param ServesRequestInfo $request_info
	 *
	 * @return ServesUriComponents
	 */
	public function resolveUri( ServesRequestInfo $request_info );
}