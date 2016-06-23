<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Responses\AbstractHttpResponse;

/**
 * Class SimpleResponse
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class SimpleResponse extends AbstractHttpResponse
{
	protected function getBody() : string
	{
	}
	
	public function getCharsetToTest() : string
	{
		return $this->getCharset();
	}
	
	public function getContentTypeToTest() : string
	{
		return $this->getContentType();
	}
	
	public function getHttpCodeToTest() : string
	{
		return $this->getHttpCode();
	}
}