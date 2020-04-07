<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use UnexpectedValueException;

interface ProvidesRequestData extends ServerRequestInterface
{
	/**
	 * @param string   $key
	 * @param int|null $default
	 *
	 * @return int
	 * @throws UnexpectedValueException if key is not set and $default is NULL or value of key is not castable as integer
	 */
	public function getInputInt( string $key, ?int $default = null ) : int;

	/**
	 * @param string     $key
	 * @param float|null $default
	 *
	 * @return float
	 * @throws UnexpectedValueException if key is not set and $default is NULL or value of key is not castable as float
	 */
	public function getInputFloat( string $key, ?float $default = null ) : float;

	/**
	 * @param string      $key
	 * @param string|null $default
	 *
	 * @return string
	 * @throws UnexpectedValueException if key is not set and $default is NULL or value for key is not a string
	 */
	public function getInputString( string $key, ?string $default = null ) : string;

	/**
	 * @param string            $key
	 * @param array<mixed>|null $default
	 *
	 * @return array<mixed>
	 * @throws UnexpectedValueException if key is not set and $default is null or value for key is not an array
	 */
	public function getInputArray( string $key, ?array $default = null ) : array;

	public function hasInputKey( string $key ) : bool;

	public function isInputNull( string $key ) : bool;
}