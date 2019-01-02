<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages\Interfaces;

use IceHawk\IceHawk\Exceptions\RuntimeException;
use Psr\Http\Message\ServerRequestInterface;

interface ProvidesRequestData extends ServerRequestInterface
{
	/**
	 * @param string      $key
	 * @param string|null $default
	 *
	 * @throws RuntimeException if key is not set and $default is NULL or value for key is not a string
	 * @return string
	 */
	public function getInputString( string $key, ?string $default = null ) : string;

	/**
	 * @param string     $key
	 * @param array|null $default
	 *
	 * @throws RuntimeException if key is not set and $default is null or value for key is not an array
	 * @return array
	 */
	public function getInputArray( string $key, ?array $default = null ) : array;
}