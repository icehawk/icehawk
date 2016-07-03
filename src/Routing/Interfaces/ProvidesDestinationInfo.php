<?php
namespace IceHawk\IceHawk\Routing\Interfaces;

/**
 * Interface ProvidesDestinationInfo
 * @package IceHawk\IceHawk\Routing\Interfaces
 */
interface ProvidesDestinationInfo
{
	public function getUri() : string;

	public function getRequestMethod() : string;
}