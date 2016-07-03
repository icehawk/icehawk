<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface HandlesWriteRequest
 * @package IceHawk\IceHawk\RequestHandlers\Interfaces
 */
interface HandlesWriteRequest extends HandlesRequest
{
	public function handle( ProvidesWriteRequestData $request );
}