<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use Psr\Http\Message\UploadedFileInterface;
use function is_array;

final class UploadedFilesCollection
{
	/** @var array|UploadedFileInterface[] */
	private $uploadedFiles;

	private function __construct()
	{
		$this->uploadedFiles = [];
	}

	public static function fromFilesArray( array $filesArray ) : self
	{
		$collection = new self();
		$flatArray  = [];

		foreach ( $filesArray as $field => $files )
		{
			foreach ( (array)$files as $keyField => $values )
			{
				if ( is_array( $values ) )
				{
					foreach ( $values as $index => $value )
					{
						$flatArray[ $field ][ $index ][ $keyField ] = $value;
					}

					continue;
				}

				$flatArray[ $field ][0][ $keyField ] = $values;
			}
		}

		foreach ( $flatArray as $field => $files )
		{
			foreach ( $files as $fileArray )
			{
				$collection->addFile( (string)$field, UploadedFile::fromArray( $fileArray ) );
			}
		}

		return $collection;
	}

	private function addFile( string $field, UploadedFileInterface $uploadedFile ) : void
	{
		$this->uploadedFiles[ $field ][] = $uploadedFile;
	}

	public static function fromGlobals() : self
	{
		return self::fromFilesArray( $_FILES ?? [] );
	}

	public static function fromUploadedFilesArray( array $uploadedFiles ) : self
	{
		$collection = new self();

		foreach ( $uploadedFiles as $field => $files )
		{
			$collection->addFiles( $field, $files );
		}

		return $collection;
	}

	private function addFiles( string $field, array $files ) : void
	{
		foreach ( $files as $file )
		{
			$this->addFile( $field, $file );
		}
	}

	public function toArray() : array
	{
		return $this->uploadedFiles;
	}
}