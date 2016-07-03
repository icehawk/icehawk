<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Routing\Interfaces;

/**
 * Interface ProvidesMatchResult
 * @package IceHawk\IceHawk\Routing\Interfaces
 */
interface ProvidesMatchResult
{
	public function matches( string $other ) : bool;

	public function getMatches() : array;
}