<?php
namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface ParsesContent
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface ParsesRequestBody
{
	public function parse( string $body ) : array;
}