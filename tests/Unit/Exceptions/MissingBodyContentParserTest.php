<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Exceptions;

use Fortuneglobe\IceHawk\Exceptions\MissingBodyContentParser;

/**
 * Class MissingBodyContentParserTest
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Exceptions
 */
class MissingBodyContentParserTest extends \PHPUnit_Framework_TestCase
{
	public function contentTypeProvider()
	{
		return [ [ 'application/json' ], [ 'application/xml' ], [ 'application/x-www-form-urlencoded' ], [ '' ] ];
	}

	/**
	 * @dataProvider contentTypeProvider
	 */
	public function testIntegrity( string $expectedContentType )
	{
		$ex = ( new MissingBodyContentParser() )->withContentType( $expectedContentType );
		
		$this->assertEquals( $expectedContentType, $ex->getContentType() );
	}
}