<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit;

use IceHawk\IceHawk\IceHawk;
use PHPUnit\Framework\TestCase;

final class IceHawkTest extends TestCase
{
	/**
	 * @throws \PHPUnit\Framework\ExpectationFailedException
	 * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
	 */
	public function testCanCreateInstance() : void
	{
		$this->assertInstanceOf( IceHawk::class, IceHawk::withDependencies() );
	}
}
