<?php
namespace Fortuneglobe\IceHawk\Exceptions;

/**
 * Class MissingParser
 *
 * @package Fortuneglobe\IceHawk\Exceptions
 */
final class MissingBodyContentParser extends IceHawkException
{
	private $contentType;

	public function withContentType( string $contentType ) : self
	{
		$this->contentType = $contentType;
		
		return $this;
	}

	public function getContentType()
	{
		return $this->contentType;
	}

}