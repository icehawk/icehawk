<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\RequestHandlers\Interfaces;

use Fortuneglobe\IceHawk\Interfaces\ProvidesReadRequestData;

/**
 * Interface HandlesReadRequest
 * @package Fortuneglobe\IceHawk\RequestHandlers\Interfaces
 */
interface HandlesReadRequest
{
	public function handle( ProvidesReadRequestData $request );
}