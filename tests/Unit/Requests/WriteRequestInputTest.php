<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Requests;

use Fortuneglobe\IceHawk\RequestParsers\FormBodyParser;
use Fortuneglobe\IceHawk\RequestParsers\NullParser;
use Fortuneglobe\IceHawk\Requests\UploadedFile;
use Fortuneglobe\IceHawk\Requests\WriteRequestInput;
use Fortuneglobe\IceHawk\Tests\Unit\Mocks\PhpStreamMock;

class WriteRequestInputTest extends \PHPUnit_Framework_TestCase
{
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
		$writeRequestInput = new WriteRequestInput( $writeData, new NullParser() );

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
		$writeRequestInput = new WriteRequestInput( $writeData, new NullParser() );

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
		$_FILES = $uploadedFiles;

		$writeRequestInput = new WriteRequestInput( [ ], new NullParser() );

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
		$writeRequestInput = new WriteRequestInput( [ ], new NullParser() );
		$oneFile           = $writeRequestInput->getOneFile( 'test' );

		$this->assertNull( $oneFile );
	}

	public function testGetOneFileReturnNullIfFileIndexIsNotSet()
	{
		$_FILES = [
			'test_file' => [
				'name'     => 'TestFile.dat',
				'tmp_name' => '/tmp/TestFile.dat',
				'type'     => 'text/plain',
				'size'     => 1024,
				'error'    => UPLOAD_ERR_OK,
			],
		];

		$writeRequestInput = new WriteRequestInput( [ ], new NullParser() );
		$oneFile           = $writeRequestInput->getOneFile( 'test_file', 1 );

		$this->assertNull( $oneFile );
	}

	public function testGetFilesReturnsEmptyArrayIfFieldKeyIsNotSet()
	{
		$writeRequestInput = new WriteRequestInput( [ ], new NullParser() );
		$files             = $writeRequestInput->getFiles( 'test' );

		$this->assertInternalType( 'array', $files );
		$this->assertEmpty( $files );
	}

	public function testCanGetBodyDataFromInputStream()
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', 'Unit-Test' );

		$writeRequestInput = new WriteRequestInput( [ ], new NullParser() );

		$this->assertEquals( 'Unit-Test', $writeRequestInput->getBody() );

		stream_wrapper_restore( "php" );
	}

	public function testGetBodyReturnsEmptyStringIfIsEmpty()
	{
		$writeRequestInput = new WriteRequestInput( [ ], new NullParser() );

		$this->assertSame( '', $writeRequestInput->getBody() );
	}

	public function postAndBodyDataProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit=tested',
				'unit',
			    'tested'
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test=units',
				'test',
			    'units'
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit[test]=units',
				'unit',
				[ 'test' => 'units' ]
			],
		];
	}

	/**
	 * @dataProvider postAndBodyDataProvider
	 */
	public function testBodyParamsOverwritesPostParams( array $postData, string $body, string $key, $expectedValue )
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', $body );
		
		$_POST = $postData;
		$writeRequestInput = new WriteRequestInput( [], new FormBodyParser() );

		$this->assertEquals( $expectedValue, $writeRequestInput->get( $key ) );
		
		stream_wrapper_restore( "php" );
	}

	public function uriAndBodyDataProvider()
	{
		return [
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'unit=tested',
				'unit',
				'test'
			],
			[
				[ 'unit' => 'test', 'test' => 'unit' ],
				'test=units',
				'test',
				'unit'
			],
			[
				[ 'unit' => [ 'test' => 'unit' ] ],
				'unit[test]=units',
				'unit',
				[ 'test' => 'unit' ]
			],
		];
	}

	/**
	 * @dataProvider uriAndBodyDataProvider
	 */
	public function testUriParamsOverwritesBodyParams( array $uriData, string $body, string $key, $expectedValue )
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', $body );

		$writeRequestInput = new WriteRequestInput( $uriData, new FormBodyParser() );

		$this->assertEquals( $expectedValue, $writeRequestInput->get( $key ) );

		stream_wrapper_restore( "php" );
	}
}
