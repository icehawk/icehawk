<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Dependencies;

use Closure;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;

abstract class AbstractDependencies implements ResolvesDependencies
{
	/** @var array<string, mixed> */
	private array $pool = [];

	/**
	 * @param string  $identifier
	 * @param Closure $createFunction
	 *
	 * @return mixed
	 */
	final protected function getInstance( string $identifier, Closure $createFunction )
	{
		return $this->pool[ $identifier ] ??= $createFunction->call( $this );
	}
}