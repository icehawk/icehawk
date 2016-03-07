<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface BuildsRequests
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface BuildsRequests
{
	public function buildRequest( array $getData, array $postData, array $uploadedFiles ) : ServesRequestData;
}