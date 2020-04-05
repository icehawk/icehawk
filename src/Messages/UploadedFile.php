<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use function move_uploaded_file;

final class UploadedFile implements UploadedFileInterface
{
	private string $name;

	private string $type;

	private string $tempName;

	private int $error;

	private int $size;

	private function __construct( string $name, string $type, string $tempName, int $error, int $size )
	{
		$this->name     = $name;
		$this->type     = $type;
		$this->tempName = $tempName;
		$this->error    = $error;
		$this->size     = $size;
	}

	/**
	 * @param array<string, string|int> $fileData
	 *
	 * @return UploadedFileInterface
	 */
	public static function fromArray( array $fileData ) : UploadedFileInterface
	{
		return new self(
			(string)$fileData['name'],
			(string)$fileData['type'],
			(string)$fileData['tmp_name'],
			(int)$fileData['error'],
			(int)$fileData['size']
		);
	}

	/**
	 * @return StreamInterface
	 * @throws InvalidArgumentException
	 */
	public function getStream() : StreamInterface
	{
		return new Stream( $this->tempName, 'rb' );
	}

	/**
	 * @param string $targetPath
	 *
	 * @throws RuntimeException
	 */
	public function moveTo( $targetPath ) : void
	{
		if ( !move_uploaded_file( $this->tempName, $targetPath ) )
		{
			throw new RuntimeException( 'Could not move uploaded file.' );
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