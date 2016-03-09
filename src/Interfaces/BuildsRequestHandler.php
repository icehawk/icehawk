<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface BuildsDomainRequestHandlers
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface BuildsRequestHandler
{
	public function build( ProvidesRequestData $request ) : HandlesRequest;
}
