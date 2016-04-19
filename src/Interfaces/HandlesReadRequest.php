<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesReadRequest
 * @package Fortuneglobe\IceHawk\RequestHandlers\Interfaces
 */
interface HandlesReadRequest extends HandlesRequest
{
	public function handle( ProvidesReadRequestData $request ) : ServesResponse;
}