<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Interfaces;

use Countable;
use IceHawk\IceHawk\Types\HttpMethod;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int, HttpMethod>
 */
interface HttpMethodsInterface extends IteratorAggregate, Countable
{
	public function add( HttpMethod $httpMethod, HttpMethod ...$httpMethods ) : void;

	/**
	 * @return array<string>
	 */
	public function asStringArray() : array;
}