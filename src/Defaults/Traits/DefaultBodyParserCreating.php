<?php
namespace Fortuneglobe\IceHawk\Defaults\Traits;

use Fortuneglobe\IceHawk\RequestParsers\AbstractBodyParserFactory;
use Fortuneglobe\IceHawk\RequestParsers\SimpleBodyParserFactory;

/**
 * Trait DefaultRequestBodyParsing
 *
 * @package Fortuneglobe\IceHawk\Defaults\Traits
 */
trait DefaultBodyParserCreating
{
	public function getBodyParserFactory() : AbstractBodyParserFactory
	{
		return new SimpleBodyParserFactory();
	}
}