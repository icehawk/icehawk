<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class UriTest extends TestCase
{
	/**
	 * @param string $url
	 * @param string $expectedAuthority
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider authorityProvider
	 */
	public function testCanGetAuthority( string $url, string $expectedAuthority ) : void
	{
		$uri = Uri::fromString( $url );

		$this->assertSame( $expectedAuthority, $uri->getAuthority() );
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function authorityProvider() : array
	{
		return [
			[
				'url'               => 'https://example.com',
				'expectedAuthority' => 'example.com',
			],
			[
				'url'               => 'https://example.com:8080',
				'expectedAuthority' => 'example.com:8080',
			],
			[
				'url'               => 'https://user@example.com:8080',
				'expectedAuthority' => 'user@example.com:8080',
			],
			[
				'url'               => 'https://user:pass@example.com:8080',
				'expectedAuthority' => 'user:pass@example.com:8080',
			],
			# Do not output port, if scheme and port match defaults (http => 80, https => 443)
			[
				'url'               => 'https://user:pass@example.com:443',
				'expectedAuthority' => 'user:pass@example.com',
			],
			[
				'url'               => 'http://user:pass@example.com:80',
				'expectedAuthority' => 'user:pass@example.com',
			],
		];
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testWithHost() : void
	{
		$url = 'https://example.com';
		$uri = Uri::fromString( $url );

		$this->assertSame( 'example.com', $uri->getHost() );

		$newUri = $uri->withHost( 'phpunit.de' );

		$this->assertNotSame( $uri, $newUri );
		$this->assertNotEquals( $uri, $newUri );
		$this->assertSame( 'phpunit.de', $newUri->getHost() );
	}

	/**
	 * @param string $url
	 * @param string $expectedPath
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider pathProvider
	 */
	public function testGetPath( string $url, string $expectedPath ) : void
	{
		$uri = Uri::fromString( $url );

		$this->assertSame( $expectedPath, $uri->getPath() );
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function pathProvider() : array
	{
		return [
			[
				'url'          => 'https://example.com',
				'expectedPath' => '',
			],
			[
				'url'          => 'https://example.com/',
				'expectedPath' => '/',
			],
			[
				'url'          => 'https://example.com/some/path',
				'expectedPath' => '/some/path',
			],
			[
				'url'          => 'https://example.com/some/path/index.html',
				'expectedPath' => '/some/path/index.html',
			],
			[
				'url'          => 'https://example.com/some/path/index.html?var=value',
				'expectedPath' => '/some/path/index.html',
			],
		];
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithPath() : void
	{
		$url = 'https://example.com';
		$uri = Uri::fromString( $url );

		$this->assertSame( '', $uri->getPath() );

		$newUri = $uri->withPath( '/some/path' );

		$this->assertNotSame( $uri, $newUri );
		$this->assertNotEquals( $uri, $newUri );
		$this->assertSame( '/some/path', $newUri->getPath() );
	}

	/**
	 * @param string   $url
	 * @param int|null $expectedPort
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider portProvider
	 */
	public function testGetPort( string $url, ?int $expectedPort ) : void
	{
		$uri = Uri::fromString( $url );

		$this->assertSame( $expectedPort, $uri->getPort() );
	}

	/**
	 * @return array<array<string, null|int|string>>
	 */
	public function portProvider() : array
	{
		return [
			[
				'url'          => 'https://example.com',
				'expectedPort' => null,
			],
			[
				'url'          => 'https://example.com:8080',
				'expectedPort' => 8080,
			],
		];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetHost() : void
	{
		$uri = Uri::fromString( 'https://example.com/test.html' );

		$this->assertSame( 'example.com', $uri->getHost() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithFragment() : void
	{
		$uri = Uri::fromString( 'https://example.com/test.html' );

		$this->assertSame( '', $uri->getFragment() );

		$newUri = $uri->withFragment( 'anchor' );

		$this->assertNotSame( $uri, $newUri );
		$this->assertNotEquals( $uri, $newUri );
		$this->assertSame( 'anchor', $newUri->getFragment() );
	}

	/**
	 * @param string $url
	 * @param string $expectedFragment
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider fragmentProvider
	 */
	public function testGetFragment( string $url, string $expectedFragment ) : void
	{
		$uri = Uri::fromString( $url );

		$this->assertSame( $expectedFragment, $uri->getFragment() );
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function fragmentProvider() : array
	{
		return [
			[
				'url'              => 'https://example.com',
				'expectedFragment' => '',
			],
			[
				'url'              => 'https://example.com#anchor',
				'expectedFragment' => 'anchor',
			],
			[
				'url'              => 'https://example.com/path/to#anchor',
				'expectedFragment' => 'anchor',
			],
			[
				'url'              => 'https://example.com/path/to/index.html#anchor',
				'expectedFragment' => 'anchor',
			],
		];
	}

	/**
	 * @param string $url
	 * @param string $expectedUserInfo
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider userInfoProvider
	 */
	public function testGetUserInfo( string $url, string $expectedUserInfo ) : void
	{
		$uri = Uri::fromString( $url );

		$this->assertSame( $expectedUserInfo, $uri->getUserInfo() );
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function userInfoProvider() : array
	{
		return [
			[
				'url'              => 'https://example.com',
				'expectedUserInfo' => '',
			],
			[
				'url'              => 'https://user@example.com:8080',
				'expectedUserInfo' => 'user',
			],
			[
				'url'              => 'https://user:pass@example.com',
				'expectedUserInfo' => 'user:pass',
			],
			[
				'url'              => 'https://:pass@example.com',
				'expectedUserInfo' => ':pass',
			],
		];
	}

	/**
	 * @param string $url
	 * @param string $expectedScheme
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider schemeProvider
	 */
	public function testGetScheme( string $url, string $expectedScheme ) : void
	{
		$uri = Uri::fromString( $url );

		$this->assertSame( $expectedScheme, $uri->getScheme() );
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function schemeProvider() : array
	{
		return [
			[
				'url'            => 'https://example.com',
				'expectedScheme' => 'https',
			],
			[
				'url'            => 'http://example.com',
				'expectedScheme' => 'http',
			],
			[
				'url'            => '//example.com',
				'expectedScheme' => '',
			],
		];
	}

	/**
	 * @param array<string, string|int|null> $components
	 * @param string                         $expectedUrl
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider toStringProvider
	 */
	public function test__toString( array $components, string $expectedUrl ) : void
	{
		$uri = Uri::fromComponents( $components );

		$this->assertSame( $expectedUrl, (string)$uri );
	}

	/**
	 * @return array<array<string, string|array<string, string|int|null>>>
	 */
	public function toStringProvider() : array
	{
		return [
			[
				'components'  => [
					'scheme'   => 'https',
					'user'     => 'user',
					'pass'     => 'pass',
					'host'     => 'example.com',
					'port'     => 8080,
					'path'     => '/some/path',
					'query'    => 'var=value',
					'fragment' => 'anchor',
				],
				'expectedUrl' => 'https://user:pass@example.com:8080/some/path?var=value#anchor',
			],
			[
				'components'  => [
					'scheme'   => '',
					'user'     => 'user',
					'pass'     => 'pass',
					'host'     => 'example.com',
					'port'     => 8080,
					'path'     => '/some/path',
					'query'    => 'var=value',
					'fragment' => 'anchor',
				],
				'expectedUrl' => '//user:pass@example.com:8080/some/path?var=value#anchor',
			],
			[
				'components'  => [
					'scheme'   => '',
					'user'     => '',
					'pass'     => 'pass',
					'host'     => 'example.com',
					'port'     => 8080,
					'path'     => '/some/path',
					'query'    => 'var=value',
					'fragment' => 'anchor',
				],
				'expectedUrl' => '//:pass@example.com:8080/some/path?var=value#anchor',
			],
			[
				'components'  => [
					'scheme'   => '',
					'user'     => '',
					'pass'     => '',
					'host'     => 'example.com',
					'port'     => 8080,
					'path'     => '/some/path',
					'query'    => 'var=value',
					'fragment' => 'anchor',
				],
				'expectedUrl' => '//example.com:8080/some/path?var=value#anchor',
			],
			[
				'components'  => [
					'scheme'   => '',
					'user'     => '',
					'pass'     => '',
					'host'     => 'example.com',
					'port'     => null,
					'path'     => '/some/path',
					'query'    => 'var=value',
					'fragment' => 'anchor',
				],
				'expectedUrl' => '//example.com/some/path?var=value#anchor',
			],
			[
				'components'  => [
					'scheme'   => '',
					'user'     => '',
					'pass'     => '',
					'host'     => 'example.com',
					'port'     => null,
					'path'     => '/some/path',
					'query'    => '',
					'fragment' => 'anchor',
				],
				'expectedUrl' => '//example.com/some/path#anchor',
			],
			[
				'components'  => [
					'scheme'   => '',
					'user'     => '',
					'pass'     => '',
					'host'     => 'example.com',
					'port'     => null,
					'path'     => '',
					'query'    => '',
					'fragment' => '',
				],
				'expectedUrl' => '//example.com',
			],
			# Do not output port, if scheme and port match defaults (http => 80, https => 443)
			[
				'components'  => [
					'scheme'   => 'http',
					'user'     => '',
					'pass'     => '',
					'host'     => 'example.com',
					'port'     => 80,
					'path'     => '/some/path',
					'query'    => '',
					'fragment' => '',
				],
				'expectedUrl' => 'http://example.com/some/path',
			],
			[
				'components'  => [
					'scheme'   => 'https',
					'user'     => '',
					'pass'     => '',
					'host'     => 'example.com',
					'port'     => 443,
					'path'     => '/some/path',
					'query'    => '',
					'fragment' => '',
				],
				'expectedUrl' => 'https://example.com/some/path',
			],
			# Remove query string from path if present
			[
				'components'  => [
					'scheme'   => 'https',
					'user'     => '',
					'pass'     => '',
					'host'     => 'example.com',
					'port'     => 443,
					'path'     => '/some/path?testkey=should-be-removed',
					'query'    => 'key=value',
					'fragment' => '',
				],
				'expectedUrl' => 'https://example.com/some/path?key=value',
			],
		];
	}

	/**
	 * @param array<string, string|int|null> $components
	 *
	 * @throws InvalidArgumentException
	 * @dataProvider invalidComponentsProvider
	 */
	public function testThrowsExceptionForInvalidComponents( array $components ) : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid URI components.' );

		/** @noinspection UnusedFunctionResultInspection */
		Uri::fromComponents( $components );
	}

	/**
	 * @return array<array<string, array<string, string|null>>>
	 */
	public function invalidComponentsProvider() : array
	{
		return [
			[
				'components' => [
					'scheme'   => '',
					'user'     => '',
					'pass'     => '',
					'host'     => '',
					'port'     => null,
					'path'     => '',
					'query'    => '',
					'fragment' => '',
				],
			],
			[
				'components' => [
					'scheme'   => '',
					'user'     => '',
					'pass'     => '',
					'host'     => '',
					'port'     => null,
					'path'     => '/some/path',
					'query'    => '',
					'fragment' => '',
				],
			],
		];
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithUserInfo() : void
	{
		$uri = Uri::fromString( 'https://user:pass@example.com' );

		$this->assertSame( 'user:pass', $uri->getUserInfo() );

		$newUri = $uri->withUserInfo( 'me', 'testing' );

		$this->assertNotSame( $uri, $newUri );
		$this->assertNotEquals( $uri, $newUri );

		$this->assertSame( 'me:testing', $newUri->getUserInfo() );
	}

	/**
	 * @param string $url
	 * @param string $expectedQuery
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider queryProvider
	 */
	public function testGetQuery( string $url, string $expectedQuery ) : void
	{
		$uri = Uri::fromString( $url );

		$this->assertSame( $expectedQuery, $uri->getQuery() );
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function queryProvider() : array
	{
		return [
			[
				'url'           => 'https://example.com',
				'expectedQuery' => '',
			],
			[
				'url'           => 'https://example.com?var=value',
				'expectedQuery' => 'var=value',
			],
		];
	}

	/**
	 * @param array<string, string|int|null> $components
	 * @param string                         $expectedUrl
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider toStringProvider
	 */
	public function testFromComponents( array $components, string $expectedUrl ) : void
	{
		$uri = Uri::fromComponents( $components );

		$this->assertSame( $expectedUrl, (string)$uri );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithPort() : void
	{
		$uri = Uri::fromString( 'https://example.com:80' );

		$this->assertSame( 80, $uri->getPort() );

		$newUri = $uri->withPort( null );

		$this->assertNotSame( $uri, $newUri );
		$this->assertNotEquals( $uri, $newUri );
		$this->assertNull( $newUri->getPort() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithQuery() : void
	{
		$uri = Uri::fromString( 'https://example.com?var=value' );

		$this->assertSame( 'var=value', $uri->getQuery() );

		$newUri = $uri->withQuery( 'var=value&foo=bar' );

		$this->assertNotSame( $uri, $newUri );
		$this->assertNotEquals( $uri, $newUri );
		$this->assertSame( 'var=value&foo=bar', $newUri->getQuery() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testFromString() : void
	{
		$uri = Uri::fromString( 'https://user:pass@example.com:8080/some/path/?var=value#anchor' );

		$this->assertSame( 'https', $uri->getScheme() );
		$this->assertSame( 'user:pass', $uri->getUserInfo() );
		$this->assertSame( 'user:pass@example.com:8080', $uri->getAuthority() );
		$this->assertSame( 'example.com', $uri->getHost() );
		$this->assertSame( 8080, $uri->getPort() );
		$this->assertSame( '/some/path/', $uri->getPath() );
		$this->assertSame( 'var=value', $uri->getQuery() );
		$this->assertSame( 'anchor', $uri->getFragment() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testWithScheme() : void
	{
		$uri = Uri::fromString( '//example.com?var=value' );

		$this->assertSame( '', $uri->getScheme() );

		$newUri = $uri->withScheme( 'https' );

		$this->assertNotSame( $uri, $newUri );
		$this->assertNotEquals( $uri, $newUri );
		$this->assertSame( 'https', $newUri->getScheme() );
	}
}
