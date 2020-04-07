<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Fixtures\Traits;

use IceHawk\IceHawk\Messages\UploadedFile;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

trait UploadedFilesProviding
{
	private function filesArray() : array
	{
		return [
			'file1' => [
				'name'     => 'test.txt',
				'type'     => 'text/plain',
				'tmp_name' => '/tmp/php/efgwef98',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 563,
			],
			'file2' => [
				'name'     => 'test.txt',
				'type'     => 'text/plain',
				'tmp_name' => '/tmp/php/khjgjkhg43',
				'error'    => UPLOAD_ERR_NO_FILE,
				'size'     => 0,
			],
		];
	}

	private function nestedFilesArray() : array
	{
		return [
			'files' => [
				'name'     => ['test1.txt', 'test2.json'],
				'type'     => ['text/plain', 'application/json'],
				'tmp_name' => ['/tmp/php/efgwef98', '/tmp/php/khjgjkhg43'],
				'error'    => [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE],
				'size'     => [563, 0],
			],
		];
	}

	private function uploadedFilesArray() : array
	{
		return [
			'test' => [
				'foo' => UploadedFile::fromArray(
					[
						'name'     => 'test.txt',
						'type'     => 'text/plain',
						'tmp_name' => '/tmp/php/efgwef98',
						'error'    => UPLOAD_ERR_OK,
						'size'     => 563,
					]
				),
				'bar' => UploadedFile::fromArray(
					[
						'name'     => 'test.txt',
						'type'     => 'text/plain',
						'tmp_name' => '/tmp/php/lkhkl43',
						'error'    => UPLOAD_ERR_NO_FILE,
						'size'     => 0,
					]
				),
			],
		];
	}
}
