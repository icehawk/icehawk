<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Test\Unit\Uri;

use Fortuneglobe\IceHawk\Constants\Http;
use Fortuneglobe\IceHawk\Interfaces\ServesResponse;
use Fortuneglobe\IceHawk\RequestInfo;
use Fortuneglobe\IceHawk\Responses\Redirect;
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
}
