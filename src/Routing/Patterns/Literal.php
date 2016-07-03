<?php
/**
 * @author hollodotme
 */

namespace IceHawk\IceHawk\Routing\Patterns;

use IceHawk\IceHawk\Routing\Interfaces\ProvidesMatchResult;

/**
 * Class Literal
 * @package IceHawk\IceHawk\Routing\Patterns
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