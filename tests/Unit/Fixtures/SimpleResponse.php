<?php
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
	
	public function getHttpCodeToTest() : string
	{
		return $this->getHttpCode();
	}
}