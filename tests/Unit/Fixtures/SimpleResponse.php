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

namespace IceHawk\IceHawk\Tests\Unit\Fixtures;

use IceHawk\IceHawk\Responses\AbstractHttpResponse;

/**
 * Class SimpleResponse
 * @package IceHawk\IceHawk\Tests\Unit\Fixtures
 */
class SimpleResponse extends AbstractHttpResponse
{
	protected function getBody() : string
	{
		return '';
	}

	public function getCharsetToTest() : string
	{
		return $this->getCharset();
	}

	public function getContentTypeToTest() : string
	{
		return $this->getContentType();
	}

	public function getHttpCodeToTest() : int
	{
		return $this->getHttpCode();
	}
}
