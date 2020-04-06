<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\UploadedFilesCollection;
use IceHawk\IceHawk\Tests\Fixtures\Traits\UploadedFilesProviding;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class UploadedFilesCollectionTest extends TestCase
{
	use UploadedFilesProviding;

	private const EXPECTED_FILES_COUNT = 2;

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testItCanBeCreatedFromUploadFilesArray() : void
	{
		$files           = [];
		$filesCollection = UploadedFilesCollection::fromUploadedFilesArray( $files );

		$this->assertEmpty( $filesCollection->toArray() );

		$files           = $this->uploadedFilesArray();
		$filesCollection = UploadedFilesCollection::fromUploadedFilesArray( $files );

		$this->assertCount( self::EXPECTED_FILES_COUNT, $filesCollection->toArray()['test'] );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testItCanBeCreatedFromFilesArray() : void
	{
		$files           = [];
		$filesCollection = UploadedFilesCollection::fromFilesArray( $files );

		$this->assertEmpty( $filesCollection->toArray() );

		$files           = $this->filesArray();
		$filesCollection = UploadedFilesCollection::fromFilesArray( $files );

		$this->assertCount( self::EXPECTED_FILES_COUNT, $filesCollection );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testItCanBeCreatedFromGlobals() : void
	{
		$_FILES = [];

		$this->assertEmpty( UploadedFilesCollection::fromGlobals()->toArray() );

		$_FILES = $this->filesArray();

		$this->assertCount( self::EXPECTED_FILES_COUNT, UploadedFilesCollection::fromGlobals() );
		$this->assertCount( self::EXPECTED_FILES_COUNT, UploadedFilesCollection::fromGlobals()->toArray() );
	}
}
