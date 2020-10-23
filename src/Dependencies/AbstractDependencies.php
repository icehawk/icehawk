<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Dependencies;

use Closure;
use IceHawk\IceHawk\Interfaces\ResolvesDependencies;
use function debug_backtrace;
use const DEBUG_BACKTRACE_IGNORE_ARGS;

abstract class AbstractDependencies implements ResolvesDependencies
{
	/** @var array<string, mixed> */
	private array $pool = [];

	/**
	 * @param Closure     $createFunction
	 * @param string|null $identifier
	 *
	 * @return mixed
	 */
	final protected function getInstance( Closure $createFunction, ?string $identifier = null ) : mixed
	{
		$identifier ??= $this->getCallingMethod();

		return $this->pool[ $identifier ] ??= $createFunction->call( $this );
	}

	private function getCallingMethod() : string
	{
		/** @var array<string, mixed> $caller */
		$caller = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 3 )[2];

		return $caller['class'] . '::' . $caller['function'];
	}
}