<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Routing\Patterns;

use Fortuneglobe\IceHawk\Routing\Interfaces\ProvidesMatchResult;

/**
 * Class RegExp
 * @package Fortuneglobe\IceHawk\Routing\Patterns
 */
final class RegExp implements ProvidesMatchResult
{
	/** @var string */
	private $regExp;

	/** @var array */
	private $matchKeys;

	/** @var array */
	private $matchValues;

	public function __construct( $regExp, array $matchKeys = [ ] )
	{
		$this->regExp      = $regExp;
		$this->matchKeys   = array_values( $matchKeys );
		$this->matchValues = [ ];
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
			for ( $i = 0; $i < count( $this->matchKeys ); $i++ )
			{
				$key   = $this->matchKeys[ $i ];
				$value = $this->matchValues[ $i + 1 ] ?? null;

				$matches[ $key ] = $value;
			}
		}

		return $matches;
	}
}