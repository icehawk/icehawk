<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Dependencies\Exceptions;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;
use Throwable;

final class RetrievingEntryFailedException extends RuntimeException implements ContainerExceptionInterface
{
	public static function forId( string $id, Throwable $previous ) : self
	{
		return new self(
			sprintf( 'Could not retrieve entry from container for ID: %s - %s', $id, $previous->getMessage() ),
			$previous
		);
	}

	private function __construct( string $message, Throwable $previous )
	{
		parent::__construct( $message, $previous->getCode(), $previous );
	}
}