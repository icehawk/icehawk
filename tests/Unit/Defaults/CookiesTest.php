<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Tests\Unit\Defaults;

use IceHawk\IceHawk\Defaults\Cookies;

class CookiesTest extends \PHPUnit\Framework\TestCase
{
	public function testEmptyCookiesReturnsEmptyArray()
	{
		$cookies = new Cookies( [] );

		$this->assertEmpty( $cookies->getData() );
	}

	public function testCanGetAllCookiesAsAssocArray()
	{
		$cookieData = [
			'name'    => 'Unit.Test.Cookie',
			'payload' => 'Some Test Data',
		];

		$cookies = new Cookies( $cookieData );

		$this->assertEquals( $cookieData, $cookies->getData() );
	}

	public function testCanGetSingleCookie()
	{
		$cookieData = [
			'name'    => 'Unit.Test.Cookie',
			'payload' => 'Some Test Data',
		];

		$cookies = new Cookies( $cookieData );

		$this->assertEquals( 'Unit.Test.Cookie', $cookies->get( 'name' ) );
		$this->assertEquals( 'Some Test Data', $cookies->get( 'payload' ) );
	}

	public function testNotExistingValueReturnsDefaultValue()
	{
		$cookieData = [
			'name'    => 'Unit.Test.Cookie',
			'payload' => 'Some Test Data',
		];

		$cookies = new Cookies( $cookieData );

		$this->assertEquals( 'Unit.Test.Cookie', $cookies->get( 'name', false ) );
		$this->assertEquals( 'Some Test Data', $cookies->get( 'payload', false ) );
		$this->assertNull( $cookies->get( 'not-existing' ) );
		$this->assertFalse( $cookies->get( 'not-existing', false ) );
		$this->assertEquals( 'default value', $cookies->get( 'not-existing', 'default value' ) );
	}

	public function testConstructionFromEnvRepresentsGlobalState()
	{
		$_COOKIE = [
			'name'    => 'Unit.Test.Cookie',
			'payload' => 'Some Test Data',
		];

		$cookies = Cookies::fromEnv();

		$this->assertEquals( $_COOKIE, $cookies->getData() );
	}
}
