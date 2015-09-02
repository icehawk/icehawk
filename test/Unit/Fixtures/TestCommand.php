<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Fixtures;

use Fortuneglobe\IceHawk\DomainCommand;

/**
 * Class TestCommand
 *
 * @package Fortuneglobe\IceHawk\Test\Unit\Fixtures
 */
class TestCommand extends DomainCommand
{
	public function getTestValue()
	{
		return $this->getRequestValue( 'testValue' );
	}

	public function getTestData()
	{
		return $this->getRequestData();
	}

	public function getAllTestFiles()
	{
		return $this->getAllUploadedFiles();
	}

	public function getTestFiles()
	{
		return $this->getUploadedFiles( 'testFiles' );
	}

	public function getTestFile()
	{
		return $this->getOneUploadedFile( 'testFiles', 1 );
	}

	public function getBody()
	{
		return $this->getRequestRawData();
	}
}