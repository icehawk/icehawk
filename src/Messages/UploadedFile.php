<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use function move_uploaded_file;
use function rename;
use const PHP_SAPI;
use const UPLOAD_ERR_NO_FILE;

final class UploadedFile implements UploadedFileInterface
{
	/**
	 * @param array<string, string|int> $fileData
	 *
	 * @return UploadedFileInterface
	 */
	#[Pure]
	public static function fromArray( array $fileData ) : UploadedFileInterface
	{
		return new self(
			(string)($fileData['name'] ?? 'unknown'),
			(string)($fileData['type'] ?? 'unknown'),
			(string)($fileData['tmp_name'] ?? 'unknown'),
			(int)($fileData['error'] ?? UPLOAD_ERR_NO_FILE),
			(int)($fileData['size'] ?? 0)
		);
	}

	private function __construct(
		private string $name,
		private string $type,
		private string $tempName,
		private int $error,
		private int $size
	)
	{
	}

	/**
	 * @return StreamInterface
	 * @throws InvalidArgumentException
	 */
	public function getStream() : StreamInterface
	{
		return Stream::fromFile( $this->tempName );
	}

	/**
	 * @param string $targetPath
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function moveTo( $targetPath ) : void
	{
		$this->guardTargetPathIsValid( $targetPath );

		$moved = 'cli' === PHP_SAPI
			? @rename( $this->tempName, $targetPath )
			: @move_uploaded_file( $this->tempName, $targetPath );

		if ( !$moved )
		{
			throw new RuntimeException( 'Could not move uploaded file' );
		}
	}

	/**
	 * @param mixed $targetPath
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardTargetPathIsValid( mixed $targetPath ) : void
	{
		if ( !is_string( $targetPath ) || '' === trim( $targetPath ) )
		{
			throw new InvalidArgumentException( 'Target path is empty' );
		}
	}

	public function getSize() : int
	{
		return $this->size;
	}

	public function getError() : int
	{
		return $this->error;
	}

	public function getClientFilename() : string
	{
		return $this->name;
	}

	public function getClientMediaType() : string
	{
		return $this->type;
	}
}
