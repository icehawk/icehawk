<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing\Interfaces;

/**
 * Interface ProvidesMatchResult
 * @package Fortuneglobe\IceHawk\Routing\Interfaces
 */
interface ProvidesMatchResult
{
	public function matches( string $other ) : bool;

	public function getMatches() : array;
}