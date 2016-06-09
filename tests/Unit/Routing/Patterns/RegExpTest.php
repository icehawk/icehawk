<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Routing\Patterns;

use Fortuneglobe\IceHawk\Routing\Patterns\RegExp;

class RegExpTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @param string $pattern
	 * @param string $other
	 * @param bool   $expectedResult
	 *
	 * @dataProvider regExpMatchProvider
	 */
	public function testCanMatch( string $pattern, string $other, bool $expectedResult )
	{
		$regExp = new RegExp( $pattern );

		$result = $regExp->matches( $other );

		$this->assertSame( $expectedResult, $result );
	}

	public function regExpMatchProvider()
	{
		return [
			[ '##', '', true ],
			[ '#/#', '/', true ],
			[ '#/path#', '/path', true ],
			[ '#/(unit|test)#', '/unit', true ],
			[ '#/(unit|test)#', '/test', true ],
			[ '#^/(unit|test)$#', '/unit/test', false ],
		];
	}

	/**
	 * @param string $pattern
	 * @param string $other
	 * @param array  $matchKeys
	 * @param array  $expectedMatches
	 *
	 * @dataProvider regExpMatchesProvider
	 */
	public function testCanGetMatches( string $pattern, string $other, array $matchKeys, array $expectedMatches )
	{
		$regExp = new RegExp( $pattern, $matchKeys );

		$regExp->matches( $other );

		$this->assertEquals( $expectedMatches, $regExp->getMatches() );
	}

	public function regExpMatchesProvider()
	{
		return [
			# Simply match 1:1
			[
				'pattern'         => '#^/path/to/(somewhere|anywhere)$#',
				'other'           => '/path/to/somewhere',
				'matchKeys'       => [ 'where' ],
				'expectedMatches' => [ 'where' => 'somewhere' ],
			],
			# Simply match 1:1
			[
				'pattern'         => '#^/path/to/(somewhere|anywhere)$#',
				'other'           => '/path/to/anywhere',
				'matchKeys'       => [ 'where' ],
				'expectedMatches' => [ 'where' => 'anywhere' ],
			],
			# Test matchKeys not empty, but no matchValues
			[
				'pattern'         => '#^/path/to/(somewhere|anywhere)$#',
				'other'           => '/path/to/elsewhere',
				'matchKeys'       => [ 'where' ],
				'expectedMatches' => [ ],
			],
			# Test more matchKeys than matchValues, matchKeys are preserved, but null
			[
				'pattern'         => '#^/path/to/(somewhere|anywhere)$#',
				'other'           => '/path/to/anywhere',
				'matchKeys'       => [ 'where', 'to', 'go' ],
				'expectedMatches' => [ 'where' => 'anywhere', 'to' => null, 'go' => null ],
			],
			# Test empty matchKeys when matchValues are not empty
			[
				'pattern'         => '#^/path/to/(somewhere|anywhere)$#',
				'other'           => '/path/to/anywhere',
				'matchKeys'       => [ ],
				'expectedMatches' => [ ],
			],
			# Test keys in matchKeys array are ignored
			[
				'pattern'         => '#^/path/to/(somewhere|anywhere)$#',
				'other'           => '/path/to/anywhere',
				'matchKeys'       => [ '1' => 'where' ],
				'expectedMatches' => [ 'where' => 'anywhere' ],
			],
		];
	}
}
