<?php
namespace Fortuneglobe\IceHawk\Tests\Unit\Defaults;

use Fortuneglobe\IceHawk\Constants\HttpCode;
use Fortuneglobe\IceHawk\Defaults\RequestInfo;
use Fortuneglobe\IceHawk\Defaults\UriRewriter;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\Responses\Redirect;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestUriRewriterWithInvalidMap;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestUriRewriterWithValidMap;

class UriRewriterTest extends \PHPUnit_Framework_TestCase
{
	public function uriProvider()
	{
		return [
			[ 'unit-test' ],
			[ '/unit-test' ],
			[ 'unit/test' ],
		];
	}

	/**
	 * @dataProvider uriProvider
	 */
    public function testDefaultsToPermanentRedirectOfUri( $uri )
    {
	    $uriRewriter = new UriRewriter();
	    $redirect = $uriRewriter->rewrite( new RequestInfo( [ 'REQUEST_URI' => $uri ] ) );

	    $this->assertInstanceOf( ServesResponse::class, $redirect );
	    $this->assertInstanceOf( Redirect::class, $redirect );
	    
	    $this->assertTrue( $redirect->urlEquals( $uri ) );
	    $this->assertTrue( $redirect->codeEquals( HttpCode::MOVED_PERMANENTLY ) );
    }

	public function validMapUriProvider()
	{
		return [
			[ '/non/regex/rewrite', '/non_regex_rewrite', HttpCode::MOVED_PERMANENTLY ],
			[ '/non/regex/no/code', '/non_regex_no_code', HttpCode::MOVED_PERMANENTLY ],
			[ '/regex/rewrite', '/regex_rewrite', HttpCode::FOUND ],
			[ '/regex/rewrite/', '/regex_rewrite', HttpCode::FOUND ],
			[ '/regex/param/test', '/regex_param_test', HttpCode::FOUND ],
			[ '/regex/param/test/', '/regex_param_test', HttpCode::FOUND ],
			[ '/not/existing/in/map', '/not/existing/in/map', HttpCode::MOVED_PERMANENTLY ],
		];
	}
	
	/**
	 * @dataProvider validMapUriProvider
	 */
	public function testRewriteWithValidMap( $uri, $exprectedRedirectUrl, $expectedRedirectCode )
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => $uri ] );

		$rewriter = new TestUriRewriterWithValidMap();
		$redirect = $rewriter->rewrite( $requestInfo );

		$this->assertInstanceOf( ServesResponse::class, $redirect );
		$this->assertInstanceOf( Redirect::class, $redirect );

		$this->assertTrue( $redirect->urlEquals( $exprectedRedirectUrl ) );
		$this->assertTrue( $redirect->codeEquals( $expectedRedirectCode ) );
	}

	public function invalidMapUriProvider()
	{
		return [
			[ '/empty/redirect/data' ],
			[ '/non/array/redirect/data' ],
		];
	}

	/**
	 * @dataProvider invalidMapUriProvider
	 * @expectedException \Fortuneglobe\IceHawk\Exceptions\MissingRedirectUrlInRewriteMap
	 */
	public function testRewriteWithInvalidMap( $uri )
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => $uri ] );

		$rewriter = new TestUriRewriterWithInvalidMap();
		$rewriter->rewrite( $requestInfo );
	}
}