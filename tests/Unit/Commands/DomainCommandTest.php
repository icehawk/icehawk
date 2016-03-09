<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Commands;

use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Requests\UploadedFile;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestCommand;
use Fortuneglobe\IceHawk\Tests\Unit\Mocks\PhpStreamMock;

class DomainCommandTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAccessValuesFromRequest()
	{
		$postData    = [ 'testValue' => 'Unit-Test' ];
		$postRequest = new WriteRequest( RequestInfo::fromEnv(), $postData, [ ] );

		$command = new TestCommand( $postRequest );

		$this->assertEquals( 'Unit-Test', $command->getTestValue() );
		$this->assertEquals( $postData, $command->getTestData() );
	}

	public function testCanAccessUploadedFilesFromRequest()
	{
		$uploadedFilesArray = [
			'testFiles' => [
				'name'     => [ 'test1.file', 'test2.file' ],
				'tmp_name' => [ '/tmp/test1.file', '/tmp/test2.file' ],
				'type'     => [ 'text/plain', 'text/plain' ],
				'size'     => [ 1024, 2048 ],
				'error'    => [ UPLOAD_ERR_OK, UPLOAD_ERR_OK ],
			],
			'fileTests' => [
				'name'     => [ 'test3.file', 'test4.file' ],
				'tmp_name' => [ '/tmp/test3.file', '/tmp/test4.file' ],
				'type'     => [ 'text/plain', 'text/plain' ],
				'size'     => [ 3072, 4096 ],
				'error'    => [ UPLOAD_ERR_OK, UPLOAD_ERR_OK ],
			]
		];

		$expectedUploadedFiles = [
			'testFiles' => [
				new UploadedFile( 'test1.file', '/tmp/test1.file', 'text/plain', 1024, UPLOAD_ERR_OK ),
				new UploadedFile( 'test2.file', '/tmp/test2.file', 'text/plain', 2048, UPLOAD_ERR_OK ),
			],
			'fileTests' => [
				new UploadedFile( 'test3.file', '/tmp/test3.file', 'text/plain', 3072, UPLOAD_ERR_OK ),
				new UploadedFile( 'test4.file', '/tmp/test4.file', 'text/plain', 4096, UPLOAD_ERR_OK ),
			],
		];

		$postRequest = new WriteRequest( RequestInfo::fromEnv(), [ ], $uploadedFilesArray );

		$command = new TestCommand( $postRequest );

		$this->assertEquals( $expectedUploadedFiles, $command->getAllTestFiles() );
		$this->assertEquals( $expectedUploadedFiles['testFiles'], $command->getTestFiles() );
		$this->assertEquals( $expectedUploadedFiles['testFiles'][1], $command->getTestFile() );
	}

	public function testCanAccessRawDataFromRequest()
	{
		stream_wrapper_unregister( "php" );
		stream_wrapper_register( "php", PhpStreamMock::class );
		file_put_contents( 'php://input', 'Unit-Test' );

		$postRequest = new WriteRequest( RequestInfo::fromEnv(), [ ], [ ] );
		$command     = new TestCommand( $postRequest );

		$this->assertEquals( 'Unit-Test', $command->getBody() );

		stream_wrapper_restore( "php" );
	}

	public function testCanAccessRequestInfoFromRequest()
	{
		$requestInfo = RequestInfo::fromEnv();
		$postRequest = new WriteRequest( $requestInfo, [ ], [ ] );
		$command     = new TestCommand( $postRequest );

		$this->assertSame( $requestInfo, $command->getRequestInfo() );
	}
}
