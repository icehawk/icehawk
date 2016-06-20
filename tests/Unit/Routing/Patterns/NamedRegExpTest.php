<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Routing\Patterns;

use Fortuneglobe\IceHawk\Routing\Patterns\NamedRegExp;

class NamedRegExpTest extends \PHPUnit_Framework_TestCase
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
		$regExp = new NamedRegExp( $pattern );

		$result = $regExp->matches( $other );

		$this->assertSame( $expectedResult, $result );
	}

	public function regExpMatchProvider()
	{
		return [
			[ '', '', true ],
			[ '/', '/', true ],
			[ '/path', '/path', true ],
			[ '/(unit|test)', '/unit', true ],
			[ '/(unit|test)', '/test', true ],
			[ '^/(unit|test)$', '/unit/test', false ],
			[ '/unit', '/unit/test', true ],
		];
	}

	/**
	 * @param string $pattern
	 * @param string $other
	 * @param array  $expectedMatches
	 *
	 * @dataProvider regExpMatchesProvider
	 */
	public function testCanGetMatches( string $pattern, string $other, array $expectedMatches )
	{
		$regExp = new NamedRegExp( $pattern );

		$regExp->matches( $other );

		$this->assertEquals( $expectedMatches, $regExp->getMatches() );
	}

	public function regExpMatchesProvider()
	{
		return [
			# Simply match 1:1
			[
				'pattern'         => '^/path/to/(?<where>somewhere|anywhere)$',
				'other'           => '/path/to/somewhere',
				'expectedMatches' => [ 'where' => 'somewhere' ],
			],
			# Simply match 1:1
			[
				'pattern'         => '^/path/to/(?<where>somewhere|anywhere)$',
				'other'           => '/path/to/anywhere',
				'expectedMatches' => [ 'where' => 'anywhere' ],
			],
			# No matchValues
			[
				'pattern'         => '^/path/to/(?<where>somewhere|anywhere)$',
				'other'           => '/path/to/elsewhere',
				'expectedMatches' => [ ],
			],
			# Test more matches
			[
				'pattern'         => '^/path/to/(?<where>somewhere|anywhere)/(?<to>\d*)/(?<go>.*)$',
				'other'           => '/path/to/anywhere/123/here',
				'expectedMatches' => [ 'where' => 'anywhere', 'to' => '123', 'go' => 'here' ],
			],
			# Test empty matchKeys when named groups are missing
			[
				'pattern'         => '^/path/to/(somewhere|anywhere)$',
				'other'           => '/path/to/anywhere',
				'expectedMatches' => [ ],
			],
		];
	}
}
