<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ServesGetRequestData
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProvidesReadRequestData extends ProvidesRequestData
{
	public function getInputData() : ProvidesReadRequestInputData;
}
