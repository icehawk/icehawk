<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Requests;

use Fortuneglobe\IceHawk\Requests\UploadedFileInfo;

class UploadedFileInfoTest extends \PHPUnit_Framework_TestCase
{
	public function testInstanceFromFileArrayEqualsInstanceFromConstruct()
	{
		$fileArray = [
			'name'     => 'TestFile.tmp',
			'tmp_name' => '/tmp/abc123',
			'type'     => 'text/plain',
			'size'     => 1024,
			'error'    => UPLOAD_ERR_OK
		];

		$uploadedFileInfoFromConstruct = new UploadedFileInfo(
			$fileArray['name'], $fileArray['tmp_name'], $fileArray['type'], $fileArray['size'], $fileArray['error']
		);

		$uploadedFileInfoFromFileArray = UploadedFileInfo::fromFileArray( $fileArray );

		$this->assertEquals( $uploadedFileInfoFromConstruct, $uploadedFileInfoFromFileArray );
	}

	/**
	 * @param UploadedFileInfo $uploadedFileInfo
	 * @param boolean          $expectedBool
	 *
	 * @dataProvider uploadSucceededProvider
	 */
	public function testDidUploadSucceed( UploadedFileInfo $uploadedFileInfo, $expectedBool )
	{
		$this->assertInternalType( 'boolean', $uploadedFileInfo->didUploadSucceed() );
		$this->assertSame( $expectedBool, $uploadedFileInfo->didUploadSucceed() );
	}

	public function uploadSucceededProvider()
	{
		$name    = 'UnitTest.file';
		$tmpName = '/tmp/unitTest';
		$type    = 'text/plain';
		$size    = 1024;

		return [
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_OK ), true ],
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_CANT_WRITE ), false ],
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_EXTENSION ), false ],
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_FORM_SIZE ), false ],
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_INI_SIZE ), false ],
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_FILE ), false ],
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_TMP_DIR ), false ],
			[ new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_PARTIAL ), false ],
		];
	}

	/**
	 * @param UploadedFileInfo $uploadedFileInfo
	 * @param int              $expectedErrorCode
	 * @param string           $expectedErrorMessage
	 *
	 * @dataProvider uploadErrorMessageProvider
	 */
	public function testGetErrorMessage( UploadedFileInfo $uploadedFileInfo, $expectedErrorCode, $expectedErrorMessage )
	{
		$this->assertSame( $expectedErrorCode, $uploadedFileInfo->getError() );
		$this->assertEquals( $expectedErrorMessage, $uploadedFileInfo->getErrorMessage() );
	}

	public function uploadErrorMessageProvider()
	{
		$name    = 'UnitTest.file';
		$tmpName = '/tmp/unitTest';
		$type    = 'text/plain';
		$size    = 1024;

		return [
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_OK ),
				0, ''
			],
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_INI_SIZE ),
				1, 'Filesize exceeded max size allowed by server.'
			],
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_FORM_SIZE ),
				2, 'Filesize exceeded max size allowed by input form.'
			],
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_PARTIAL ),
				3, 'File was uploaded partially.'
			],
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_FILE ),
				4, 'No file uploaded.'
			],
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_NO_TMP_DIR ),
				6, 'No upload temp directory available.'
			],
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_CANT_WRITE ),
				7, 'Cannot write file.'
			],
			[
				new UploadedFileInfo( $name, $tmpName, $type, $size, UPLOAD_ERR_EXTENSION ),
				8, 'Upload canceled by PHP extension.'
			],
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
		$uploadedFileInfo = new UploadedFileInfo(
			'UnitTest.file', $filePath, 'application/x-unit-test', 1024, UPLOAD_ERR_OK
		);

		$this->assertEquals( $expectedRealType, $uploadedFileInfo->getRealType() );
	}

	public function filePathRealTypeProvider()
	{
		return [
			[ __DIR__ . '/../Fixures/UploadedFiles/UnitTest.png', 'image/png' ],
			[ __DIR__ . '/../Fixures/UploadedFiles/UnitTest.txt', 'text/plain' ],
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
		$uploadedFileInfo = new UploadedFileInfo(
			'UnitTest.file', $filePath, 'application/x-unit-test', 1024, UPLOAD_ERR_OK
		);

		$this->assertEquals( $expectedEncoding, $uploadedFileInfo->getEncoding() );
	}

	public function filePathEncodingProvider()
	{
		return [
			[ __DIR__ . '/../Fixures/UploadedFiles/UnitTest.png', 'binary' ],
			[ __DIR__ . '/../Fixures/UploadedFiles/UnitTest.txt', 'utf-8' ],
		];
	}
}
