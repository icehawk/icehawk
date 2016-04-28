<?php
namespace Fortuneglobe\IceHawk\RequestParsers;

use Fortuneglobe\IceHawk\Exceptions\MissingBodyContentParser;
use Fortuneglobe\IceHawk\Interfaces\ParsesRequestBody;

/**
 * Class SimpleBodyParserFactory
 *
 * @package Fortuneglobe\IceHawk\RequestParsers
 */
class SimpleBodyParserFactory extends AbstractBodyParserFactory
{
	protected function createParserByContentType( string $contentType ) : ParsesRequestBody
	{
		if ( empty($contentType) || $contentType == 'application/x-www-form-urlencoded' )
		{
			return new FormBodyParser();
		}

		throw ( new MissingBodyContentParser() )->withContentType( $contentType );
	}

	protected function canCreateParserForContentType( string $contentType ) : bool
	{
		return ( empty($contentType) || $contentType == 'application/x-www-form-urlencoded' );
	}

}