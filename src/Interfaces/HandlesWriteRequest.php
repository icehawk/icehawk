<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface HandlesWriteRequest
 * @package Fortuneglobe\IceHawk\RequestHandlers\Interfaces
 */
interface HandlesWriteRequest extends HandlesRequest
{
	public function handle( ProvidesWriteRequestData $request );
}