<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Defaults\WriteRequestResolver;

class WriteRequestResolverTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
	 */
    public function testIfDefaultResolveHandlingThrowsException()
    {
        $writeRequestResolver = new WriteRequestResolver();
	    $writeRequestResolver->resolve( new RequestInfo( [] ) );
    }
}