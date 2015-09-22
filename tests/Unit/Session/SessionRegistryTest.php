<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Session;

use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestSessionRegistry;

class SessionRegistryTest extends \PHPUnit_Framework_TestCase
{
	public function testValuesAreSetByReference()
	{
		$dataArray       = [ ];
		$sessionRegistry = new TestSessionRegistry( $dataArray );

		$sessionRegistry->setTestValue( 'Unit-Test' );

		$this->assertEquals( [ TestSessionRegistry::TEST_VALUE => 'Unit-Test' ], $dataArray );
	}

	public function testCanSetAndGetValues()
	{
		$dataArray       = [ ];
		$sessionRegistry = new TestSessionRegistry( $dataArray );

		$sessionRegistry->setTestValue( 'Unit-Test' );

		$this->assertEquals( 'Unit-Test', $sessionRegistry->getTestValue() );
	}

	public function testCanUnsetValues()
	{
		$dataArray       = [ ];
		$sessionRegistry = new TestSessionRegistry( $dataArray );

		$sessionRegistry->setTestValue( 'Unit-Test' );
		$sessionRegistry->unsetTestValue();

		$this->assertNull( $sessionRegistry->getTestValue() );
	}

	public function testNotSetValuesReturnNull()
	{
		$dataArray       = [ ];
		$sessionRegistry = new TestSessionRegistry( $dataArray );

		$this->assertNull( $sessionRegistry->getTestValue() );
	}

	public function testCanCheckIfValueIsSet()
	{
		$dataArray       = [ ];
		$sessionRegistry = new TestSessionRegistry( $dataArray );

		$sessionRegistry->setTestValue( 'Unit-Test' );

		$this->assertTrue( $sessionRegistry->isTestValueSet() );

		$sessionRegistry->unsetTestValue();

		$this->assertFalse( $sessionRegistry->isTestValueSet() );
	}
}
