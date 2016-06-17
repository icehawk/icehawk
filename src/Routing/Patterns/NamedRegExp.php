<?php
namespace Fortuneglobe\IceHawk\Routing\Patterns;

use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesMatchResult;

/**
 * Class ExactRegExp
 *
 * @package Fortuneglobe\IceHawk\Routing\Patterns
 */
class NamedRegExp implements ProvidesMatchResult
{
	/** @var string */
	private $regExp;

	/** @var array */
	private $matchValues;

	public function __construct( string $regExp )
	{
		$this->regExp = $regExp;
	}

	public function matches( string $other ) : bool
	{
		return (bool)preg_match( $this->regExp, $other, $this->matchValues );
	}

	public function getMatches() : array
	{
		$matches = [ ];

		if ( !empty($this->matchValues) )
		{
			foreach ( $this->matchValues as $key => $value )
			{
				if ( is_string( $key ) )
				{
					$matches[ $key ] = $value;
				}
			}
		}

		return $matches;
	}
}