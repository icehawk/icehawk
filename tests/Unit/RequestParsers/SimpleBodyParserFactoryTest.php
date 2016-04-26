<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\RequestParsers;

use Fortuneglobe\IceHawk\Interfaces\ParsesRequestBody;
use Fortuneglobe\IceHawk\RequestParsers\FormBodyParser;
use Fortuneglobe\IceHawk\RequestParsers\NullParser;
use Fortuneglobe\IceHawk\RequestParsers\SimpleBodyParserFactory;

/**
 * Class SimpleBodyParserFactoryTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\RequestParsers
 */
class SimpleBodyParserFactoryTest extends \PHPUnit_Framework_TestCase
{
	public function contentTypeProvider()
	{
		return [
			[ '', new FormBodyParser() ], [ 'application/x-www-form-urlencoded', new FormBodyParser() ],
			[ 'application/json', new NullParser() ], [ 'text/html', new NullParser() ],
			[ 'application/xml', new NullParser() ], [ 'text/xml', new NullParser() ],
		];
	}

	/**
	 * @dataProvider contentTypeProvider
	 */
	public function testIfCorrectParserReturnedByContentType( string $contentType, ParsesRequestBody $expectedParser )
	{
		$parserFactory = new SimpleBodyParserFactory();
		
		$parser = $parserFactory->selectParserByContentType( $contentType );
		
		$this->assertEquals( $expectedParser, $parser );
	}
}