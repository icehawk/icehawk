<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Requests;

use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\PostRequest;
use Fortuneglobe\IceHawk\Requests\UploadedFile;
use Fortuneglobe\IceHawk\Tests\Unit\Mocks\PhpStreamMock;

class PostRequestTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider requestDataProvider
	 */
	public function testCanGetRequestValueByKey( array $postData, $key, $expectedValue )
	{
		$postRequest = new PostRequest( RequestInfo::fromEnv(), $postData, [ ] );

		$this->assertEquals( $expectedValue, $postRequest->get( $key ) );
	}

	public function requestDataProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit',
				'test'
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test',
				'unit'
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit',
				[ 'test' => 'unit' ]
			],
		];
	}

	/**
	 * @dataProvider nullKeyDataProvider
	 */
	public function testGetterReturnsNullIfKeyIsNotSet( array $postData, $key )
	{
		$postRequest = new PostRequest( RequestInfo::fromEnv(), $postData, [ ] );

		$this->assertNull( $postRequest->get( $key ) );
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
	 * @dataProvider uploadedFilesProvider
	 */
	public function testCanGetOneUploadedFile(
		array $uploadedFiles, $fieldKey, $fileIndex,
		$expectedFileName, $expectedType, $expectedSize, $expectedTmpName,
		$expectedError, $expectedErrorMessage, $expectedSuccess
	)
	{
		$postRequest = new PostRequest( RequestInfo::fromEnv(), [ ], $uploadedFiles );
		$oneFile     = $postRequest->getOneFile( $fieldKey, $fileIndex );

		$this->assertInstanceOf( UploadedFile::class, $oneFile );
		$this->assertEquals( $expectedFileName, $oneFile->getName() );
		$this->assertEquals( $expectedType, $oneFile->getType() );
		$this->assertEquals( $expectedSize, $oneFile->getSize() );
		$this->assertEquals( $expectedTmpName, $oneFile->getTmpName() );
		$this->assertEquals( $expectedError, $oneFile->getError() );
		$this->assertEquals( $expectedErrorMessage, $oneFile->getErrorMessage() );
		$this->assertSame( $expectedSuccess, $oneFile->didUploadSucceed() );
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
					]
				],
				'test_file',
				0,
				'TestFile.dat',
				'text/plain',
				1024,
				'/tmp/TestFile.dat',
				UPLOAD_ERR_OK,
				'',
				true
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
					]
				],
				'test_file',
				0,
				'TestFile.dat',
				'text/plain',
				1024,
				'/tmp/TestFile.dat',
				UPLOAD_ERR_OK,
				'',
				true
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
					]
				],
				'test_file',
				1,
				'FileTest.html',
				'text/html',
				2048,
				'/tmp/FileTest.html',
				UPLOAD_ERR_PARTIAL,
				'File was uploaded partially.',
				false
			]
		];
	}

	public function testGetOneFileReturnNullIfKeyIsNotSet()
	{
		$postRequest = new PostRequest( RequestInfo::fromEnv(), [ ], [ ] );
		$oneFile     = $postRequest->getOneFile( 'test' );

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
			]
		];

		$postRequest = new PostRequest( RequestInfo::fromEnv(), [ ], $uploadedFiles );
		$oneFile     = $postRequest->getOneFile( 'test_file', 1 );

		$this->assertNull( $oneFile );
	}

	public function testGetFilesReturnsEmptyArrayIfFieldKeyIsNotSet()
	{
		$postRequest = new PostRequest( RequestInfo::fromEnv(), [ ], [ ] );
		$files       = $postRequest->getFiles( 'test' );

		$this->assertInternalType( 'array', $files );
		$this->assertEmpty( $files );
	}

	public function testCanGetRawPostDataFromInputStream()
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', 'Unit-Test' );

		$postRequest = new PostRequest( RequestInfo::fromEnv(), [ ], [ ] );

		$this->assertEquals( 'Unit-Test', $postRequest->getRawData() );

		stream_wrapper_restore( "php" );
	}

	public function testGetRawDataReturnNullIfEmpty()
	{
		$postRequest = new PostRequest( RequestInfo::fromEnv(), [ ], [ ] );

		$this->assertNull( $postRequest->getRawData() );
	}
}
