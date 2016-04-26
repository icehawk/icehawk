<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\RequestParsers;

use Fortuneglobe\IceHawk\RequestParsers\FormBodyParser;

/**
 * Class FormBodyParserTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\RequestParsers
 */
class FormBodyParserTest extends \PHPUnit_Framework_TestCase
{
	public function formBodyDataProvider()
	{
		return [
			[ 'param1=value1&param2=value2', [ 'param1' => 'value1', 'param2' => 'value2' ] ],
			[
				'dataArr1[]=value1&dataArr1[]=value2&dataArr2[]=value3&dataArr2[]=value4&dataArr2[]=value5',
				[ 'dataArr1' => [ 'value1', 'value2' ], 'dataArr2' => [ 'value3', 'value4', 'value5' ] ],
			],
			[
				'assocArr1[key1]=value1&assocArr1[key2]=value2&assocArr2[key1]=value3&assocArr2[key2]=value4&assocArr2[key3]=value5',
				[
					'assocArr1' => [ 'key1' => 'value1', 'key2' => 'value2' ],
					'assocArr2' => [ 'key1' => 'value3', 'key2' => 'value4', 'key3' => 'value5' ],
				],
			],
			[
				'assocArr1[key1][]=value1&assocArr1[key1][]=value2&assocArr1[key2][]=value1&assocArr2[key1][]=value1&assocArr2[key1][]=value3&assocArr2[key2]=value5',
				[
					'assocArr1' => [ 'key1' => [ 'value1', 'value2' ], 'key2' => [ 'value1' ] ],
					'assocArr2' => [ 'key1' => [ 'value1', 'value3' ], 'key2' => 'value5' ],
				],
			],
		];
	}

	/**
	 * @dataProvider formBodyDataProvider
	 */
	public function testParsingFormBodyReturnsArrayWithParams( string $body, array $expectedArray )
	{
		$bodyParser = new FormBodyParser();

		$parsingResult = $bodyParser->parse( $body );

		$this->assertEquals( $expectedArray, $parsingResult );
	}
}