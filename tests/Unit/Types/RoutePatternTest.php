<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Messages\Uri;
use IceHawk\IceHawk\Types\RoutePattern;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class RoutePatternTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testNewFromString() : void
	{
		$this->assertSame( '!^/unit/test$!i', RoutePattern::newFromString( '^/unit/test$' )->toString() );
		$this->assertSame( '!^/unit/test$!i', RoutePattern::newFromString( '!^/unit/test$!' )->toString() );
		$this->assertSame( '!^/unit/test$!i', RoutePattern::newFromString( '!^/unit/test$!i' )->toString() );
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testNewFromStringThrowsExceptionIfPatternStringIsEmpty() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid value for RoutePattern: empty' );

		RoutePattern::newFromString( ' ' );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetMatches() : void
	{
		$uri     = Uri::fromString( 'https://example.com/unit/test' );
		$pattern = RoutePattern::newFromString( '/unit/(?<testKey>[^/]+)' );

		$this->assertTrue( $pattern->matchesUri( $uri ) );
		$this->assertSame( ['testKey' => 'test'], $pattern->getMatches() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMatchesUri() : void
	{
		$uri            = Uri::fromString( 'https://example.com/unit/test' );
		$validPattern   = RoutePattern::newFromString( '/unit/(?<testKey>[^/]+)' );
		$invalidPattern = RoutePattern::newFromString( '/unit/(?<testKey>\d+)' );

		$this->assertTrue( $validPattern->matchesUri( $uri ) );
		$this->assertFalse( $invalidPattern->matchesUri( $uri ) );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testToString() : void
	{
		$this->assertSame( '!^/unit/test$!i', RoutePattern::newFromString( '^/unit/test$' )->toString() );
		$this->assertSame( '!^/unit/test$!i', RoutePattern::newFromString( '!^/unit/test$' )->toString() );
		$this->assertSame( '!^/unit/test$!i', RoutePattern::newFromString( '^/unit/test$!i' )->toString() );
	}
}
