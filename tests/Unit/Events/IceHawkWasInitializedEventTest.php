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

namespace IceHawk\IceHawk\Tests\Unit\Events;

use IceHawk\IceHawk\Defaults\Cookies;
use IceHawk\IceHawk\Defaults\RequestInfo;
use IceHawk\IceHawk\Events\IceHawkWasInitializedEvent;

/**
 * Class IceHawkWasInitializedEventTest
 * @package IceHawk\IceHawk\Tests\Unit\Events
 */
class IceHawkWasInitializedEventTest extends \PHPUnit_Framework_TestCase
{
	public function testCanRetrieveInjectedObjects()
	{
		$requestInfo    = RequestInfo::fromEnv();
		$requestCookies = new Cookies( [] );
		$event          = new IceHawkWasInitializedEvent( $requestInfo, $requestCookies );

		$this->assertSame( $requestInfo, $event->getRequestInfo() );
		$this->assertSame( $requestCookies, $event->getRequestCookies() );
	}
}
