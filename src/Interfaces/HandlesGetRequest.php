<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesGetRequest
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface HandlesGetRequest
{
	/**
	 * @param ServesGetRequestData $request
	 */
	public function handle( ServesGetRequestData $request );
}