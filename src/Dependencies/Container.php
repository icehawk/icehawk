<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Dependencies;

use Closure;
use IceHawk\IceHawk\Dependencies\Exceptions\EntryNotFoundExcption;
use IceHawk\IceHawk\Dependencies\Exceptions\RetrievingEntryFailedException;
use IceHawk\IceHawk\Dependencies\Interfaces\ContainerInterface;
use JetBrains\PhpStorm\Pure;
use Throwable;

final class Container implements ContainerInterface
{
	/** @var array<string, mixed> */
	private array $pool;

	/**
	 * @param array<string, Closure> $registry
	 *
	 * @return Container
	 */
	#[Pure]
	public static function new( array $registry = [] ) : self
	{
		return new self( $registry );
	}

	/**
	 * @param array<string, Closure> $registry
	 */
	private function __construct( private array $registry )
	{
		$this->pool = [];
	}

	public function register( string $id, Closure $createFunction ) : void
	{
		$this->registry[ $id ] = $createFunction;
	}

	/**
	 * @param string $id
	 *
	 * @return mixed
	 * @throws RetrievingEntryFailedException
	 * @throws EntryNotFoundExcption
	 */
	public function get( string $id ) : mixed
	{
		$createFunction = $this->registry[ $id ] ?? throw EntryNotFoundExcption::forId( $id );

		try
		{
			return $this->pool[ $id ] ??= $createFunction->call( $this );
		}
		catch ( Throwable $e )
		{
			throw RetrievingEntryFailedException::forId( $id, $e );
		}
	}

	public function has( string $id ) : bool
	{
		return isset( $this->registry[ $id ] );
	}
}