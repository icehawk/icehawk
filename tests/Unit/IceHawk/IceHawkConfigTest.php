<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\IceHawk;

use Fortuneglobe\IceHawk\IceHawkConfig;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\UriResolver;
use Fortuneglobe\IceHawk\UriRewriter;

class IceHawkConfigTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaults()
	{
		$config = new IceHawkConfig();

		$this->assertEquals( [ ], $config->getEventListeners() );
		$this->assertEquals( 'Fortuneglobe\\IceHawk', $config->getDomainNamespace() );
		$this->assertEquals( new UriResolver(), $config->getUriResolver() );
		$this->assertEquals( new UriRewriter(), $config->getUriRewriter() );
		$this->assertEquals( RequestInfo::fromEnv(), $config->getRequestInfo() );
	}
}
