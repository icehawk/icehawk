<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

interface ResolvesUri
{
	/**
	 * @param ServesRequestInfo $requestInfo
	 *
	 * @return ServesUriComponents
	 */
	public function resolveUri( ServesRequestInfo $requestInfo );
}