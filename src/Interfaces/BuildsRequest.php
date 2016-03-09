<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface BuildsRequests
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface BuildsRequest
{
	public function build( array $getData, array $postData, array $uploadedFiles ) : ProvidesRequestData;
}