<?php
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

declare(strict_types = 1);
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

namespace IceHawk\IceHawk\Tests\Unit\Responses;

use IceHawk\IceHawk\Constants\HttpCode;
use IceHawk\IceHawk\Tests\Unit\Fixtures\SimpleResponse;

class AbstractHttpResponseTest extends \PHPUnit_Framework_TestCase
{
	public function testIntegrity()
	{
		$expectedContentType = 'application/json';
		$expectedHttpCode    = HttpCode::AUTHENTICATION_TIMEOUT;
		$expectedCharset     = 'latin-1';

		$response = new SimpleResponse( $expectedContentType, $expectedHttpCode, $expectedCharset );

		$this->assertEquals( $expectedContentType, $response->getContentTypeToTest() );
		$this->assertEquals( $expectedHttpCode, $response->getHttpCodeToTest() );
		$this->assertEquals( $expectedCharset, $response->getCharsetToTest() );
	}
}
