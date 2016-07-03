<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface HandlesReadRequest
 * @package IceHawk\IceHawk\RequestHandlers\Interfaces
 */
interface HandlesReadRequest extends HandlesRequest
{
	public function handle( ProvidesReadRequestData $request );
}