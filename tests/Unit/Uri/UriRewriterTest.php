<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Tests\Unit\Uri;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Responses\Redirect;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestUriRewriterWithInvalidMap;
use Fortuneglobe\IceHawk\Tests\Unit\Fixtures\TestUriRewriterWithValidMap;
use Fortuneglobe\IceHawk\UriRewriter;

class UriRewriterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider uriProvider
	 */
	public function testDefaultsToPermanentRedirectOfUri( $uri )
	{
		$requestInfo = new RequestInfo( [ 'REQUEST_URI' => $uri ] );

		$rewriter = new UriRewriter();
		$redirect = $rewriter->rewrite( $requestInfo );

		$this->assertInstanceOf( ServesResponse::class, $redirect );
		$this->assertInstanceOf( Redirect::class, $redirect );

		$this->assertTrue( $redirect->urlEquals( $uri ) );
		$this->assertTrue( $redirect->codeEquals( Http::MOVED_PERMANENTLY ) );
	}

	public function uriProvider()
	{
		return [
			[ 'unit-test' ],
			[ '/unit-test' ],
			[ 'unit/test' ],
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

	public function validMapUriProvider()
	{
		return [
			[ '/non/regex/rewrite', '/non_regex_rewrite', Http::MOVED_PERMANENTLY ],
			[ '/non/regex/no/code', '/non_regex_no_code', Http::MOVED_PERMANENTLY ],
			[ '/regex/rewrite', '/regex_rewrite', Http::MOVED_TEMPORARILY ],
			[ '/regex/rewrite/', '/regex_rewrite', Http::MOVED_TEMPORARILY ],
			[ '/not/existing/in/map', '/not/existing/in/map', Http::MOVED_PERMANENTLY ],
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

	public function invalidMapUriProvider()
	{
		return [
			[ '/empty/redirect/data' ],
			[ '/non/array/redirect/data' ],
		];
	}
}
