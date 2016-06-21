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

	/**
	 * @var string
	 */
	private $flags;

	public function __construct( string $regExp, string $flags = '' )
	{
		$this->regExp = $regExp;
		$this->flags  = $flags;
	}

	public function matches( string $other ) : bool
	{
		return (bool)preg_match( '!' . $this->regExp . '!' . $this->flags, $other, $this->matchValues );
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