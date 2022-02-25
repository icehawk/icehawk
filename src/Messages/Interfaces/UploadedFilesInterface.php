<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages\Interfaces;

use Countable;
use IteratorAggregate;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @extends IteratorAggregate<int, UploadedFileInterface>
 */
interface UploadedFilesInterface extends IteratorAggregate, Countable
{
	/**
	 * @return array<int|string, array<int, UploadedFileInterface>>
	 */
	public function toArray() : array;

	/**
	 * @param array<string, mixed> $filesArray
	 *
	 * @return UploadedFilesInterface
	 */
	public static function fromFilesArray( array $filesArray ) : UploadedFilesInterface;

	public static function fromGlobals() : UploadedFilesInterface;

	/**
	 * @param array<string, array<int|string, UploadedFileInterface>> $uploadedFiles
	 *
	 * @return UploadedFilesInterface
	 */
	public static function fromUploadedFilesArray( array $uploadedFiles ) : UploadedFilesInterface;
}