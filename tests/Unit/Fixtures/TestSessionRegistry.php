<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\SessionRegistry as BaseSessionRegistry;

/**
 * Class TestSessionRegistry
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
final class TestSessionRegistry extends BaseSessionRegistry
{
	const TEST_VALUE = 'testValue';

	public function setTestValue( $testValue )
	{
		$this->setSessionValue( self::TEST_VALUE, $testValue );
	}

	public function getTestValue()
	{
		return $this->getSessionValue( self::TEST_VALUE );
	}

	public function unsetTestValue()
	{
		$this->unsetSessionValue( self::TEST_VALUE );
	}

	public function isTestValueSet()
	{
		return $this->isSessionKeySet( self::TEST_VALUE );
	}
}
