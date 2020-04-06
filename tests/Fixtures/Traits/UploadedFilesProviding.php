<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Fixtures\Traits;

use IceHawk\IceHawk\Messages\UploadedFile;

trait UploadedFilesProviding
{
	private function filesArray() : array
	{
		return [
			[
				'name'     => 'test.txt',
				'type'     => 'text/plain',
				'tmp_name' => '/tmp/php/efgwef98',
				'error'    => UPLOAD_ERR_OK,
				'size'     => 563,
			],
			[
				'name'     => 'test.txt',
				'type'     => 'text/plain',
				'tmp_name' => '/tmp/php/khjgjkhg43',
				'error'    => UPLOAD_ERR_NO_FILE,
				'size'     => 0,
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
