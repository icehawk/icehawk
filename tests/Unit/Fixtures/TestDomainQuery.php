<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\Query;

/**
 * Class TestDomainQuery
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestQuery extends Query
{
	public function getTestValue()
	{
		return $this->getRequestValue( 'testValue' );
	}

	public function getTestData()
	{
		return $this->getRequestData();
	}
}
