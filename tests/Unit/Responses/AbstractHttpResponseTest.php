<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Responses;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\SimpleResponse;

class AbstractHttpResponseTest extends \PHPUnit_Framework_TestCase
{
	public function testIntegrity()
	{
		$expectedContentType = 'application/json';
		$expectedHttpCode = HttpCode::AUTHENTICATION_TIMEOUT;
		$expectedCharset = 'latin-1';

		$response = new SimpleResponse($expectedContentType,$expectedHttpCode, $expectedCharset );

		$this->assertEquals( $expectedContentType, $response->getContentTypeToTest() );
		$this->assertEquals( $expectedHttpCode, $response->getHttpCodeToTest() );
		$this->assertEquals( $expectedCharset, $response->getCharsetToTest() );
	}
}
