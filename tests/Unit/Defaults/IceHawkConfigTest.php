<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;


use Fortuneglobe\IceHawk\Defaults\FinalReadRequestResponder;
use Fortuneglobe\IceHawk\Defaults\FinalWriteRequestResponder;
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
		$this->assertEquals( new FinalReadRequestResponder(), $config->getFinalReadRequestResponder() );
		$this->assertEquals( new FinalWriteRequestResponder(), $config->getFinalWriteRequestResponder() );
		$this->assertEquals( new ReadRequestResolver(), $config->getReadRequestResolver() );
		$this->assertEquals( new WriteRequestResolver(), $config->getWriteRequestResolver() );
	}
}
