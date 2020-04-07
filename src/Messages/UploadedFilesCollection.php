<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use Countable;
use Iterator;
use IteratorAggregate;
use Psr\Http\Message\UploadedFileInterface;
use function count;
use function is_array;

/**
 * @implements IteratorAggregate<int, UploadedFileInterface>
 */
final class UploadedFilesCollection implements Countable, IteratorAggregate
{
	/** @var array<int,UploadedFileInterface> */
	private array $uploadedFiles;

	private function __construct()
	{
		$this->uploadedFiles = [];
	}

	/**
	 * @param array<string, string|array<string, array<int,string|int>>> $filesArray
	 *
	 * @return UploadedFilesCollection
	 */
	public static function fromFilesArray( array $filesArray ) : self
	{
		$collection = new self();

		/** @var array<string, array<int, array<string, int|string>>> $flatArray */
		$flatArray = [];

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
			/** @var array<string, int|string> $fileArray */
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

	/**
	 * @param array<string, array<int, UploadedFileInterface>> $uploadedFiles
	 *
	 * @return UploadedFilesCollection
	 */
	public static function fromUploadedFilesArray( array $uploadedFiles ) : self
	{
		$collection = new self();

		foreach ( $uploadedFiles as $field => $files )
		{
			$collection->addFiles( $field, $files );
		}

		return $collection;
	}

	/**
	 * @param string                            $field
	 * @param array<int, UploadedFileInterface> $files
	 */
	private function addFiles( string $field, array $files ) : void
	{
		foreach ( $files as $file )
		{
			$this->addFile( $field, $file );
		}
	}

	/**
	 * @return array<int, UploadedFileInterface>
	 */
	public function toArray() : array
	{
		return $this->uploadedFiles;
	}

	/**
	 * @return Iterator<int, UploadedFileInterface>
	 */
	public function getIterator() : Iterator
	{
		foreach ( $this->uploadedFiles as $field => $files )
		{
			yield from $files;
		}
	}

	public function count() : int
	{
		$count = 0;
		foreach ( $this->uploadedFiles as $field => $files )
		{
			$count += count( $files );
		}

		return $count;
	}
}