<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

interface ProvidesRequestData extends ServerRequestInterface
{
	/**
	 * @param string      $key
	 * @param string|null $default
	 *
	 * @return string
	 * @throws RuntimeException if key is not set and $default is NULL or value for key is not a string
	 */
	public function getInputString( string $key, ?string $default = null ) : string;

	/**
	 * @param string            $key
	 * @param array<mixed>|null $default
	 *
	 * @return array<mixed>
	 * @throws RuntimeException if key is not set and $default is null or value for key is not an array
	 */
	public function getInputArray( string $key, ?array $default = null ) : array;
}