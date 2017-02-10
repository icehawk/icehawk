<?php declare(strict_types = 1);
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

use IceHawk\IceHawk\Interfaces\ProvidesUploadedFileData;
use IceHawk\IceHawk\Mappers\UploadedFilesMapper;
use IceHawk\IceHawk\Requests\UploadedFile;
use IceHawk\IceHawk\Requests\WriteRequestInput;

class WriteRequestInputTest extends \PHPUnit\Framework\TestCase
{
	public function testCanGetBodyAndData()
	{
		$body = 'test';
		$data = [ 'key' => 'value' ];

		$writeRequestInput = new WriteRequestInput( 'test', $data );

		$this->assertEquals( $body, $writeRequestInput->getBody() );
		$this->assertEquals( $data, $writeRequestInput->getData() );
	}

	public function requestDataProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit',
				'test',
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test',
				'unit',
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit',
				[ 'test' => 'unit' ],
			],
		];
	}

	/**
	 * @dataProvider requestDataProvider
	 */
	public function testCanGetRequestValueByKey( array $writeData, $key, $expectedValue )
	{
		$writeRequestInput = new WriteRequestInput( '', $writeData );

		$this->assertEquals( $expectedValue, $writeRequestInput->get( $key ) );
	}

	public function nullKeyDataProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'blubb',
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'blubb',
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'blubb',
			],
		];
	}

	/**
	 * @dataProvider nullKeyDataProvider
	 */
	public function testGetterReturnsNullIfKeyIsNotSet( array $writeData, $key )
	{
		$writeRequestInput = new WriteRequestInput( '', $writeData );

		$this->assertNull( $writeRequestInput->get( $key ) );
	}

	public function uploadedFilesProvider()
	{
		return [
			# 1
			[
				[
					'test_file' => [
						'name'     => 'TestFile.dat',
						'tmp_name' => '/tmp/TestFile.dat',
						'type'     => 'text/plain',
						'size'     => 1024,
						'error'    => UPLOAD_ERR_OK,
					],
				],
				'test_file',
				0,
				'TestFile.dat',
				'text/plain',
				1024,
				'/tmp/TestFile.dat',
				UPLOAD_ERR_OK,
				'',
				true,
			],
			# 2
			[
				[
					'test_file' => [
						'name'     => [ 'TestFile.dat', 'FileTest.html' ],
						'tmp_name' => [ '/tmp/TestFile.dat', '/tmp/FileTest.html' ],
						'type'     => [ 'text/plain', 'text/html' ],
						'size'     => [ 1024, 2048 ],
						'error'    => [ UPLOAD_ERR_OK, UPLOAD_ERR_PARTIAL ],
					],
				],
				'test_file',
				0,
				'TestFile.dat',
				'text/plain',
				1024,
				'/tmp/TestFile.dat',
				UPLOAD_ERR_OK,
				'',
				true,
			],
			# 3
			[
				[
					'test_file' => [
						'name'     => [ 'TestFile.dat', 'FileTest.html' ],
						'tmp_name' => [ '/tmp/TestFile.dat', '/tmp/FileTest.html' ],
						'type'     => [ 'text/plain', 'text/html' ],
						'size'     => [ 1024, 2048 ],
						'error'    => [ UPLOAD_ERR_OK, UPLOAD_ERR_PARTIAL ],
					],
				],
				'test_file',
				1,
				'FileTest.html',
				'text/html',
				2048,
				'/tmp/FileTest.html',
				UPLOAD_ERR_PARTIAL,
				'File was uploaded partially.',
				false,
			],
			# 4: The second file has a string index
			[
				[
					'test_file' => [
						'name'     => [ 'TestFile.dat', 'file1' => 'FileTest.html' ],
						'tmp_name' => [ '/tmp/TestFile.dat', 'file1' => '/tmp/FileTest.html' ],
						'type'     => [ 'text/plain', 'file1' => 'text/html' ],
						'size'     => [ 1024, 'file1' => 2048 ],
						'error'    => [ UPLOAD_ERR_OK, 'file1' => UPLOAD_ERR_PARTIAL ],
					],
				],
				'test_file',
				'file1',
				'FileTest.html',
				'text/html',
				2048,
				'/tmp/FileTest.html',
				UPLOAD_ERR_PARTIAL,
				'File was uploaded partially.',
				false,
			],
		];
	}

	/**
	 * @dataProvider uploadedFilesProvider
	 */
	public function testCanGetOneUploadedFile(
		array $uploadedFiles, $fieldKey, $fileIndex,
		$expectedFileName, $expectedType, $expectedSize, $expectedTmpName,
		$expectedError, $expectedErrorMessage, $expectedSuccess
	)
	{
		$uploadedFiles = (new UploadedFilesMapper( $uploadedFiles ))->mapToInfoObjects();

		$writeRequestInput = new WriteRequestInput( '', [], $uploadedFiles );

		$oneFile = $writeRequestInput->getOneFile( $fieldKey, $fileIndex );

		$this->assertInstanceOf( UploadedFile::class, $oneFile );
		$this->assertEquals( $expectedFileName, $oneFile->getName() );
		$this->assertEquals( $expectedType, $oneFile->getType() );
		$this->assertEquals( $expectedSize, $oneFile->getSize() );
		$this->assertEquals( $expectedTmpName, $oneFile->getTmpName() );
		$this->assertEquals( $expectedError, $oneFile->getError() );
		$this->assertEquals( $expectedErrorMessage, $oneFile->getErrorMessage() );
		$this->assertSame( $expectedSuccess, $oneFile->didUploadSucceed() );
	}

	public function testEmptyUploadedFileIsReturnedIfKeyIsNotSet()
	{
		$writeRequestInput = new WriteRequestInput( '', [], [] );
		$oneFile           = $writeRequestInput->getOneFile( 'test' );

		$this->assertInstanceOf( ProvidesUploadedFileData::class, $oneFile );
		$this->assertSame( '', $oneFile->getName() );
		$this->assertSame( '', $oneFile->getTmpName() );
		$this->assertSame( '', $oneFile->getEncoding() );
		$this->assertSame( 0, $oneFile->getSize() );
		$this->assertSame( '', $oneFile->getRealType() );
		$this->assertSame( UPLOAD_ERR_NO_FILE, $oneFile->getError() );
		$this->assertFalse( $oneFile->didUploadSucceed() );
		$this->assertSame( 'No file uploaded.', $oneFile->getErrorMessage() );
	}

	public function testEmptyUploadedFileIsReturnedIfFileIndexIsNotSet()
	{
		$uploadedFiles = [
			'test_file' => [
				'name'     => 'TestFile.dat',
				'tmp_name' => '/tmp/TestFile.dat',
				'type'     => 'text/plain',
				'size'     => 1024,
				'error'    => UPLOAD_ERR_OK,
			],
		];

		$uploadedFiles     = (new UploadedFilesMapper( $uploadedFiles ))->mapToInfoObjects();
		$writeRequestInput = new WriteRequestInput( '', [], $uploadedFiles );
		$oneFile           = $writeRequestInput->getOneFile( 'test_file', 1 );

		$this->assertInstanceOf( ProvidesUploadedFileData::class, $oneFile );
		$this->assertSame( '', $oneFile->getName() );
		$this->assertSame( '', $oneFile->getTmpName() );
		$this->assertSame( '', $oneFile->getEncoding() );
		$this->assertSame( 0, $oneFile->getSize() );
		$this->assertSame( '', $oneFile->getRealType() );
		$this->assertSame( UPLOAD_ERR_NO_FILE, $oneFile->getError() );
		$this->assertFalse( $oneFile->didUploadSucceed() );
		$this->assertSame( 'No file uploaded.', $oneFile->getErrorMessage() );
	}

	public function testGetFilesReturnsEmptyArrayIfFieldKeyIsNotSet()
	{
		$writeRequestInput = new WriteRequestInput( '', [], [] );
		$files             = $writeRequestInput->getFiles( 'test' );

		$this->assertInternalType( 'array', $files );
		$this->assertEmpty( $files );
	}

	public function testGetAllUploadFiles()
	{
		$uploadedFiles = [
			'test_file'         => [
				'name'     => [ 'TestFile.dat', 'FileTest.html' ],
				'tmp_name' => [ '/tmp/TestFile.dat', '/tmp/FileTest.html' ],
				'type'     => [ 'text/plain', 'text/html' ],
				'size'     => [ 1024, 2048 ],
				'error'    => [ UPLOAD_ERR_OK, UPLOAD_ERR_PARTIAL ],
			],
			'another_test_file' => [
				'name'     => [ 'AnotherTestFile.dat', 'AnotherFileTest.html' ],
				'tmp_name' => [ '/tmp/AnotherTestFile.dat', '/tmp/AnotherFileTest.html' ],
				'type'     => [ 'text/plain', 'text/html' ],
				'size'     => [ 20, 40 ],
				'error'    => [ UPLOAD_ERR_OK, UPLOAD_ERR_PARTIAL ],
			],
		];

		$uploadedFiles     = (new UploadedFilesMapper( $uploadedFiles ))->mapToInfoObjects();
		$writeRequestInput = new WriteRequestInput( '', [], $uploadedFiles );

		$this->assertEquals( $uploadedFiles, $writeRequestInput->getAllFiles() );
	}

	public function testGetterReturnsDefaultValueIfProvidedAndKeyIsNotFound()
	{
		$writeRequestInput = new WriteRequestInput( '', [ 'unit' => 'test' ], [] );

		$stdObj = new \stdClass();

		$this->assertSame( [ '123' ], $writeRequestInput->get( 'someKey', [ '123' ] ) );
		$this->assertSame( '123', $writeRequestInput->get( 'someKey', '123' ) );
		$this->assertSame( 123, $writeRequestInput->get( 'someKey', 123 ) );
		$this->assertSame( $stdObj, $writeRequestInput->get( 'someKey', $stdObj ) );
		$this->assertSame( null, $writeRequestInput->get( 'someKey', null ) );
		$this->assertSame( null, $writeRequestInput->get( 'someKey' ) );

		$this->assertSame( 'test', $writeRequestInput->get( 'unit', [ '123' ] ) );
	}
}
