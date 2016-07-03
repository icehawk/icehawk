<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing\Patterns;

use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesMatchResult;

/**
 * Class Literal
 * @package Fortuneglobe\IceHawk\Routing\Patterns
 */
final class Literal implements ProvidesMatchResult
{
	/** @var string */
	private $literal;

	public function __construct( string $literal )
	{
		$this->literal = $literal;
	}

	public function matches( string $other ) : bool
	{
		return ($this->literal == $other);
	}

	public function getMatches() : array
	{
		return [ ];
	}
}