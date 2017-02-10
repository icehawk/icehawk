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

use IceHawk\IceHawk\Defaults\FinalReadResponder;
use IceHawk\IceHawk\Defaults\FinalWriteResponder;
use IceHawk\IceHawk\Defaults\IceHawkConfig;
use IceHawk\IceHawk\Defaults\RequestInfo;

class IceHawkConfigTest extends \PHPUnit\Framework\TestCase
{
	public function testDefaults()
	{
		$config = new IceHawkConfig();

		$this->assertEquals( [], $config->getEventSubscribers() );
		$this->assertEquals( RequestInfo::fromEnv(), $config->getRequestInfo() );
		$this->assertEquals( new FinalReadResponder(), $config->getFinalReadResponder() );
		$this->assertEquals( new FinalWriteResponder(), $config->getFinalWriteResponder() );
		$this->assertEquals( [], $config->getWriteRoutes() );
		$this->assertEquals( [], $config->getReadRoutes() );
	}
}
