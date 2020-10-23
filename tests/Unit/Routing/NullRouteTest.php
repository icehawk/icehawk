<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Routing;

use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Uri;
use IceHawk\IceHawk\Routing\NullRoute;
use IceHawk\IceHawk\Types\HttpMethods;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class NullRouteTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 */
	public function testGetAcceptedHttpMethods() : void
	{
		self::assertEquals( HttpMethods::all(), NullRoute::new( Request::fromGlobals() )->getAcceptedHttpMethods() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testGetModifiedRequest() : void
	{
		$request = Request::fromGlobals();

		self::assertSame( $request, NullRoute::new( $request )->getModifiedRequest() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesUri() : void
	{
		self::assertFalse(
			NullRoute::new( Request::fromGlobals() )->matchesUri( Uri::fromString( 'https://example.com' ) )
		);
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testMatchAgainstFullUri() : void
	{
		$nullRoute = NullRoute::new( Request::fromGlobals() );

		self::assertSame( $nullRoute, $nullRoute->matchAgainstFullUri() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesRequest() : void
	{
		$request = Request::fromGlobals();

		self::assertFalse( NullRoute::new( $request )->matchesRequest( $request ) );
	}

	/**
	 * @throws Exception
	 * @throws ExpectationFailedException
	 */
	public function testGetMiddlewareClassNames() : void
	{
		self::assertCount( 0, NullRoute::new( Request::fromGlobals() )->getMiddlewareClassNames() );
	}
}
