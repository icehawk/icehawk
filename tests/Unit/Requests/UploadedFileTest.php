<?php declare(strict_types=1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Tests\Unit\Requests;

use IceHawk\IceHawk\Requests\UploadedFile;
use PHPUnit\Framework\TestCase;

class UploadedFileTest extends TestCase
{
	public function testInstanceFromFileArrayEqualsInstanceFromConstruct()
	{
		$fileArray = [
			'name'     => 'TestFile.tmp',
			'tmp_name' => '/tmp/abc123',
			'type'     => 'text/plain',
			'size'     => 1024,
			'error'    => UPLOAD_ERR_OK,
		];

		$uploadedFileInfoFromConstruct = new UploadedFile(
			$fileArray['name'], $fileArray['tmp_name'], $fileArray['type'], $fileArray['size'], $fileArray['error']
		);

		$uploadedFileInfoFromFileArray = UploadedFile::fromFileArray( $fileArray );

		$this->assertEquals( $uploadedFileInfoFromConstruct, $uploadedFileInfoFromFileArray );
	}

	public function uploadSucceededProvider()
	{
		$name    = 'UnitTest.file';
		$tmpName = '/tmp/unitTest';
		$type    = 'text/plain';
		$size    = 1024;

		return [
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_OK ), true ],
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_CANT_WRITE ), false ],
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_EXTENSION ), false ],
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_FORM_SIZE ), false ],
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_INI_SIZE ), false ],
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_FILE ), false ],
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_TMP_DIR ), false ],
			[ new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_PARTIAL ), false ],
		];
	}

	/**
	 * @param UploadedFile $uploadedFileInfo
	 * @param boolean      $expectedBool
	 *
	 * @dataProvider uploadSucceededProvider
	 */
	public function testDidUploadSucceed( UploadedFile $uploadedFileInfo, $expectedBool )
	{
		$this->assertInternalType( 'boolean', $uploadedFileInfo->didUploadSucceed() );
		$this->assertSame( $expectedBool, $uploadedFileInfo->didUploadSucceed() );
	}

	public function uploadErrorMessageProvider()
	{
		$name    = 'UnitTest.file';
		$tmpName = '/tmp/unitTest';
		$type    = 'text/plain';
		$size    = 1024;

		return [
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_OK ),
				0, '',
			],
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_INI_SIZE ),
				1, 'Filesize exceeded max size allowed by server.',
			],
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_FORM_SIZE ),
				2, 'Filesize exceeded max size allowed by input form.',
			],
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_PARTIAL ),
				3, 'File was uploaded partially.',
			],
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_FILE ),
				4, 'No file uploaded.',
			],
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_TMP_DIR ),
				6, 'No upload temp directory available.',
			],
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_CANT_WRITE ),
				7, 'Cannot write file.',
			],
			[
				new UploadedFile( $name, $tmpName, $type, $size, UPLOAD_ERR_EXTENSION ),
				8, 'Upload canceled by PHP extension.',
			],
		];
	}

	/**
	 * @param UploadedFile $uploadedFileInfo
	 * @param int          $expectedErrorCode
	 * @param string       $expectedErrorMessage
	 *
	 * @dataProvider uploadErrorMessageProvider
	 */
	public function testGetErrorMessage( UploadedFile $uploadedFileInfo, $expectedErrorCode, $expectedErrorMessage )
	{
		$this->assertSame( $expectedErrorCode, $uploadedFileInfo->getError() );
		$this->assertEquals( $expectedErrorMessage, $uploadedFileInfo->getErrorMessage() );
	}

	public function filePathRealTypeProvider()
	{
		return [
			[ __DIR__ . '/../Fixtures/UploadedFiles/UnitTest.png', 'image/png' ],
			[ __DIR__ . '/../Fixtures/UploadedFiles/UnitTest.txt', 'text/plain' ],
		];
	}

	/**
	 * @param string $filePath
	 * @param string $expectedRealType
	 *
	 * @dataProvider filePathRealTypeProvider
	 */
	public function testGetRealType( $filePath, $expectedRealType )
	{
		$uploadedFileInfo = new UploadedFile(
			'UnitTest.file', $filePath, 'application/x-unit-test', 1024, UPLOAD_ERR_OK
		);

		$this->assertEquals( $expectedRealType, $uploadedFileInfo->getRealType() );
	}

	public function filePathEncodingProvider()
	{
		return [
			[ __DIR__ . '/../Fixtures/UploadedFiles/UnitTest.png', 'binary' ],
			[ __DIR__ . '/../Fixtures/UploadedFiles/UnitTest.txt', 'utf-8' ],
		];
	}

	/**
	 * @param string $filePath
	 * @param string $expectedEncoding
	 *
	 * @dataProvider filePathEncodingProvider
	 */
	public function testGetEncoding( $filePath, $expectedEncoding )
	{
		$uploadedFileInfo = new UploadedFile(
			'UnitTest.file', $filePath, 'application/x-unit-test', 1024, UPLOAD_ERR_OK
		);

		$this->assertEquals( $expectedEncoding, $uploadedFileInfo->getEncoding() );
	}
}
