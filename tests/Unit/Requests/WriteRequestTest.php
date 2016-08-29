<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Requests;

use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Mappers\UploadedFilesMapper;
use IceHawk\IceHawk\Requests\UploadedFile;
use IceHawk\IceHawk\Requests\WriteRequest;

class WriteRequestTest extends \PHPUnit_Framework_TestCase
{
	public function testCanGetBodyAndData()
	{
		$body = 'test';
		$data = [ 'key' => 'value' ];

		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), $data, 'test' );

		$this->assertEquals( $body, $writeRequestInput->getBody() );
		$this->assertEquals( $data, $writeRequestInput->getInputData() );
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
		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), $writeData, '' );

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
		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), $writeData, '' );

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
		$uploadedFiles = ( new UploadedFilesMapper( $uploadedFiles ) )->mapToInfoObjects();

		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), [], '', $uploadedFiles );

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

	public function testGetOneFileReturnNullIfKeyIsNotSet()
	{
		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), [], '', [ ] );
		$oneFile           = $writeRequestInput->getOneFile( 'test' );

		$this->assertNull( $oneFile );
	}

	public function testGetOneFileReturnNullIfFileIndexIsNotSet()
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

		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), [], '', $uploadedFiles );
		$oneFile           = $writeRequestInput->getOneFile( 'test_file', 1 );

		$this->assertNull( $oneFile );
	}

	public function testGetFilesReturnsEmptyArrayIfFieldKeyIsNotSet()
	{
		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), [], '', [ ] );
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

		$uploadedFiles     = ( new UploadedFilesMapper( $uploadedFiles ) )->mapToInfoObjects();
		$writeRequestInput = new WriteRequest( RequestInfo::fromEnv(), [], '', $uploadedFiles );

		$this->assertEquals( $uploadedFiles, $writeRequestInput->getAllFiles() );
	}
}
