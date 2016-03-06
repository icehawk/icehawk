<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesPostRequest
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface HandlesPostRequest
{
	public function handle( ServesPostRequestData $request );
}