<?php
namespace Fortuneglobe\IceHawk\RequestParsers;

use Fortuneglobe\IceHawk\Interfaces\ParsesRequestBody;

/**
 * Class AbstractBodyParserFactory
 *
 * @package Fortuneglobe\IceHawk\RequestParsers
 */
abstract class AbstractBodyParserFactory
{
	public function selectParserByContentType( string $contentType ) : ParsesRequestBody
	{
		try 
		{
			return $this->createParserByContentType( $contentType );
		}
		catch ( \Throwable $throwable )
		{
			return new NullParser();
		}
	}
	
	abstract protected function createParserByContentType( string $contentType ) : ParsesRequestBody;
}