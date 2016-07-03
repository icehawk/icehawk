<?php
/**
 * @author h.woltersdorf
 */

namespace IceHawk\IceHawk\Tests\Unit\Defaults;

use IceHawk\IceHawk\Defaults\FinalReadResponder;
use IceHawk\IceHawk\Defaults\FinalWriteResponder;
use IceHawk\IceHawk\Defaults\IceHawkConfig;
use IceHawk\IceHawk\Defaults\RequestInfo;

class IceHawkConfigTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaults()
	{
		$config = new IceHawkConfig();

		$this->assertEquals( [ ], $config->getEventSubscribers() );
		$this->assertEquals( RequestInfo::fromEnv(), $config->getRequestInfo() );
		$this->assertEquals( new FinalReadResponder(), $config->getFinalReadResponder() );
		$this->assertEquals( new FinalWriteResponder(), $config->getFinalWriteResponder() );
		$this->assertEquals( [], $config->getWriteRoutes() );
		$this->assertEquals( [], $config->getReadRoutes() );
	}
}
