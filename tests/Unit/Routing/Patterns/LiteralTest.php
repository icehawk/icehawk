<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Routing\Patterns;

use Fortuneglobe\IceHawk\Routing\Patterns\Literal;

class LiteralTest extends \PHPUnit_Framework_TestCase
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
