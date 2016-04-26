<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\RequestParsers;

use Fortuneglobe\IceHawk\RequestParsers\NullParser;

/**
 * Class NullParserTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\RequestParsers
 */
class NullParserTest extends \PHPUnit_Framework_TestCase
{
	public function testParsingBodyReturnsEmptyArray()
	{
		$body = 'param1=value1&param2=value2';

		$parser = new NullParser();

		$parsingResult = $parser->parse( $body );

		$this->assertEquals( [], $parsingResult );
	}
}