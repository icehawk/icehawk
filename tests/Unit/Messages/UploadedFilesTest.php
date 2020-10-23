<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\UploadedFile;
use IceHawk\IceHawk\Messages\UploadedFiles;
use IceHawk\IceHawk\Tests\Fixtures\Traits\UploadedFilesProviding;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

final class UploadedFilesTest extends TestCase
{
	use UploadedFilesProviding;

	private const EXPECTED_FILES_COUNT = 2;

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testItCanBeCreatedFromUploadFilesArray() : void
	{
		$files         = [];
		$uploadedFiles = UploadedFiles::fromUploadedFilesArray( $files );

		self::assertEmpty( $uploadedFiles->toArray() );

		$files         = $this->uploadedFilesArray();
		$uploadedFiles = UploadedFiles::fromUploadedFilesArray( $files );

		self::assertCount( self::EXPECTED_FILES_COUNT, $uploadedFiles->toArray()['test'] );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testItCanBeCreatedFromFilesArray() : void
	{
		$files         = [];
		$uploadedFiles = UploadedFiles::fromFilesArray( $files );

		self::assertEmpty( $uploadedFiles->toArray() );

		$files         = $this->filesArray();
		$uploadedFiles = UploadedFiles::fromFilesArray( $files );

		self::assertCount( self::EXPECTED_FILES_COUNT, $uploadedFiles );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testCanBeCreatedFromNestedFilesArray() : void
	{
		$files         = $this->nestedFilesArray();
		$uploadedFiles = UploadedFiles::fromFilesArray( $files );

		self::assertCount( self::EXPECTED_FILES_COUNT, $uploadedFiles );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testItCanBeCreatedFromGlobals() : void
	{
		$_FILES = [];

		self::assertEmpty( UploadedFiles::fromGlobals()->toArray() );

		$_FILES = $this->filesArray();

		self::assertCount( self::EXPECTED_FILES_COUNT, UploadedFiles::fromGlobals() );
		self::assertCount( self::EXPECTED_FILES_COUNT, UploadedFiles::fromGlobals()->toArray() );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testGetIterator() : void
	{
		$_FILES = $this->filesArray();

		$uploadedFiles = UploadedFiles::fromFilesArray( $_FILES );

		self::assertCount( self::EXPECTED_FILES_COUNT, iterator_to_array( $uploadedFiles->getIterator(), false ) );
		self::assertInstanceOf( UploadedFile::class, $uploadedFiles->getIterator()->current() );

		$_FILES = $this->nestedFilesArray();

		$uploadedFiles = UploadedFiles::fromFilesArray( $_FILES );

		self::assertCount( self::EXPECTED_FILES_COUNT, iterator_to_array( $uploadedFiles->getIterator(), false ) );
		self::assertInstanceOf( UploadedFile::class, $uploadedFiles->getIterator()->current() );
	}
}
