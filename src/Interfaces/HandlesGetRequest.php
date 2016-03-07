<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesGetRequest
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface HandlesGetRequest
{
	public function handle( ServesGetRequestData $request );
}