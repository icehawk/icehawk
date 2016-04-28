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
		if( $this->canCreateParserForContentType( $contentType ) )
		{
			return $this->createParserByContentType( $contentType );
		}
		
		return new NullParser();
	}
	
	abstract protected function createParserByContentType( string $contentType ) : ParsesRequestBody;
	
	abstract protected function canCreateParserForContentType( string $contentType ) : bool;
}