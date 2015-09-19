<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesPostRequest
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface HandlesPostRequest
{
	/**
	 * @param ServesPostRequestData $request
	 */
	public function handle( ServesPostRequestData $request );
}