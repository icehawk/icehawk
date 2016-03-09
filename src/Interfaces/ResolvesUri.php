<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ResolvesUri
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ResolvesUri
{
	public function resolveUri( ProvidesRequestInfo $requestInfo ) : ProvidesHandlerDemand;
}