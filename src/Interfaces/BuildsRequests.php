<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface BuildsRequests
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface BuildsRequests
{
	/**
	 * @param array $getData
	 * @param array $postData
	 * @param array $uploadedFiles
	 *
	 * @return ServesGetRequestData|ServesPostRequestData
	 */
	public function buildRequest( array $getData, array $postData, array $uploadedFiles );
}