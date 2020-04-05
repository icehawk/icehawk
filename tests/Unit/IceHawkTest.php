<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit;

use IceHawk\IceHawk\IceHawk;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class IceHawkTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws Exception
	 */
	public function testCanCreateInstance() : void
	{
		$this->assertInstanceOf( IceHawk::class, IceHawk::withDependencies() );
	}
}
