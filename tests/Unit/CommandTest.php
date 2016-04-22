<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Requests\UploadedFile;
use Fortuneglobe\IceHawk\Requests\WriteRequest;
use Fortuneglobe\IceHawk\Requests\WriteRequestInput;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestCommand;

class CommandTest extends \PHPUnit_Framework_TestCase
{
	public function testCanAccessValuesFromRequest()
	{
		$requestData  = [ 'testValue' => 'Unit-Test' ];
		$writeRequest = new WriteRequest( RequestInfo::fromEnv(), new WriteRequestInput( '', $requestData, [ ] ) );

		$command = new TestCommand( $writeRequest );

		$this->assertEquals( 'Unit-Test', $command->getTestValue() );
		$this->assertEquals( $requestData, $command->getTestData() );
	}

	public function testCanAccessUploadedFilesFromRequest()
	{
		$uploadedFiles = [
			'testFiles' => [
				new UploadedFile( 'test1.file', '/tmp/test1.file', 'text/plain', 1024, UPLOAD_ERR_OK ),
				new UploadedFile( 'test2.file', '/tmp/test2.file', 'text/plain', 2048, UPLOAD_ERR_OK ),
			],
			'fileTests' => [
				new UploadedFile( 'test3.file', '/tmp/test3.file', 'text/plain', 3072, UPLOAD_ERR_OK ),
				new UploadedFile( 'test4.file', '/tmp/test4.file', 'text/plain', 4096, UPLOAD_ERR_OK ),
			],
		];

		$writeRequest =
			new WriteRequest( RequestInfo::fromEnv(), new WriteRequestInput( '', [ ], $uploadedFiles ) );

		$command = new TestCommand( $writeRequest );

		$this->assertEquals( $uploadedFiles, $command->getAllTestFiles() );
		$this->assertEquals( $uploadedFiles['testFiles'], $command->getTestFiles() );
		$this->assertEquals( $uploadedFiles['testFiles'][1], $command->getTestFile() );
	}

	public function testCanAccessRawDataFromRequest()
	{
		$body = 'Unit-Test';

		$writeRequest = new WriteRequest( RequestInfo::fromEnv(), new WriteRequestInput( $body, [ ], [ ] ) );

		$command = new TestCommand( $writeRequest );

		$this->assertEquals( $body, $command->getBody() );
	}
	
	public function testCanAccessRequestInfo()
	{
		$requestInfo = new RequestInfo(
			[
				'REQUEST_METHOD' => 'POST',
				'REQUEST_URI'    => '/domain/valid_write_test',
			]
		);

		$writeRequest = new WriteRequest( $requestInfo, new WriteRequestInput( '', [ ], [ ] ) );

		$command = new TestCommand( $writeRequest );

		$this->assertEquals( $requestInfo, $command->getRequestInfo() );
	}
}
