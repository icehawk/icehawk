<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Routing\Patterns;

use IceHawk\IceHawk\Routing\Interfaces\ProvidesMatchResult;

/**
 * Class ExactRegExp
 * @package IceHawk\IceHawk\Routing\Patterns
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
		$matches = [];

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
