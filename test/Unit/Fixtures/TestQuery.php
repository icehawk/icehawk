<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Fixtures;

use Fortuneglobe\IceHawk\DomainQuery;

/**
 * Class TestQuery
 *
 * @package Fortuneglobe\IceHawk\Test\Unit\Fixtures
 */
class TestQuery extends DomainQuery
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