<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ResolvesUri
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ResolvesUri
{
	/**
	 * @param ServesRequestInfo $requestInfo
	 *
	 * @return ServesUriComponents
	 */
	public function resolveUri( ServesRequestInfo $requestInfo );
}