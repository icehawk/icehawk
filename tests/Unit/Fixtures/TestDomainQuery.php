<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Fixtures;

use Fortuneglobe\IceHawk\DomainQuery;

/**
 * Class TestDomainQuery
 *
 * @package Fortuneglobe\IceHawk\Tests\Unit\Fixtures
 */
class TestDomainQuery extends DomainQuery
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
