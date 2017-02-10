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

namespace IceHawk\IceHawk\Tests\Unit\Routing\Patterns;

use IceHawk\IceHawk\Routing\Patterns\Literal;

class LiteralTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * @param string $string
	 * @param string $other
	 * @param bool   $expectedResult
	 *
	 * @dataProvider literalMatchProvider
	 */
	public function testCanMatch( string $string, string $other, bool $expectedResult )
	{
		$literal = new Literal( $string );
		$result  = $literal->matches( $other );

		$this->assertSame( $expectedResult, $result );
	}

	public function literalMatchProvider()
	{
		return [
			[ '/', '/', true ],
			[ '/path', '/path', true ],
			[ '/path/to-somewhere', '/path/to-somewhere', true ],
			[ '/path', '/Path', false ],
			[ '/path', '/other-path', false ],
			[ '', '/', false ],
			[ '', '/path', false ],
		];
	}

	public function testMatchesAreAlwaysEmpty()
	{
		$literal = new Literal( '/unit/test' );

		$this->assertEmpty( $literal->getMatches() );

		$literal->matches( '/unit' );

		$this->assertEmpty( $literal->getMatches() );

		$literal->matches( '/unit/test' );

		$this->assertEmpty( $literal->getMatches() );
	}
}
