<?php
namespace Fortuneglobe\IceHawk\Routing\Interfaces;

/**
 * Interface ProvidesDestinationInfo
 *
 * @package Fortuneglobe\IceHawk\Routing\Interfaces
 */
interface ProvidesDestinationInfo
{
	public function getUri() : string;

	public function getRequestMethod() : string;
}