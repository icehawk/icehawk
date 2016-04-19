<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Defaults\ReadRequestResolver;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;

class ReadRequestResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \Fortuneglobe\IceHawk\Exceptions\UnresolvedRequest
     */
    public function testIfDefaultResolveHandlingThrowsException()
    {
        $readRequestResolver = new ReadRequestResolver();
        $readRequestResolver->resolve( new RequestInfo( [] ) );
    }
}