<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Defaults\FinalReadResponder;
use Fortuneglobe\IceHawk\Defaults\FinalWriteResponder;
use Fortuneglobe\IceHawk\Defaults\IceHawkConfig;
use Fortuneglobe\IceHawk\Defaults\ReadRequestResolver;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Defaults\UriRewriter;
use Fortuneglobe\IceHawk\Defaults\WriteRequestResolver;
use Fortuneglobe\IceHawk\RequestParsers\SimpleBodyParserFactory;

class IceHawkConfigTest extends \PHPUnit_Framework_TestCase
{
	public function testDefaults()
	{
		$config = new IceHawkConfig();

		$this->assertEquals( [ ], $config->getEventSubscribers() );
		$this->assertEquals( new UriRewriter(), $config->getUriRewriter() );
		$this->assertEquals( new UriRewriter(), $config->getUriRewriter() );
		$this->assertEquals( RequestInfo::fromEnv(), $config->getRequestInfo() );
		$this->assertEquals( new SimpleBodyParserFactory(), $config->getBodyParserFactory() );
		$this->assertEquals( new FinalReadResponder(), $config->getFinalReadRequestResponder() );
		$this->assertEquals( new FinalWriteResponder(), $config->getFinalWriteRequestResponder() );
		$this->assertEquals( new ReadRequestResolver(), $config->getReadRequestResolver() );
		$this->assertEquals( new WriteRequestResolver(), $config->getWriteRequestResolver() );
	}
}
