<?php
namespace IceHawk\IceHawk\PubSub\Interfaces;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;

/**
 * Interface CarriesEventData
 *
 * @package IceHawk\IceHawk\Interfaces
 */
interface CarriesEventData
{
	public function getRequestInfo() : ProvidesRequestInfo;
}