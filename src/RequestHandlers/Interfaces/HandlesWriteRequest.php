<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestHandlers\Interfaces;

use Fortuneglobe\IceHawk\Interfaces\ProvidesWriteRequestData;

/**
 * Interface HandlesPostRequest
 * @package Fortuneglobe\IceHawk\RequestHandlers\Interfaces
 */
interface HandlesWriteRequest
{
	public function handle( ProvidesWriteRequestData $request );
}