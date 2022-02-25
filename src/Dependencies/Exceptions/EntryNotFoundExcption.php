<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Dependencies\Exceptions;

use JetBrains\PhpStorm\Pure;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

final class EntryNotFoundExcption extends RuntimeException implements NotFoundExceptionInterface
{
	#[Pure]
	public static function forId( string $id ) : self
	{
		return new self( 'Could not find entry in container for ID: ' . $id );
	}

	#[Pure]
	private function __construct( string $message )
	{
		parent::__construct( $message );
	}
}