<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use function parse_url;

final class Uri implements UriInterface
{
	/** @var array<int|string, int|string|false|null> */
	private array $components;

	/**
	 * @param array<int|string, int|string|false|null> $components
	 */
	private function __construct( array $components )
	{
		$this->components = $components;
	}

	/**
	 * @param string $uri
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public static function fromString( string $uri ) : UriInterface
	{
		return self::fromComponents( (array)parse_url( $uri ) );
	}

	/**
	 * @param array<int|string, int|string|false|null> $components
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public static function fromComponents( array $components ) : UriInterface
	{
		$uri = new self( $components );

		if ( false === parse_url( (string)$uri ) )
		{
			throw new InvalidArgumentException( 'Invalid URI components.' );
		}

		return $uri;
	}

	public function getScheme() : string
	{
		return (string)($this->components['scheme'] ?? '');
	}

	public function getAuthority() : string
	{
		return sprintf(
			'%s%s%s',
			$this->getUserInfo() ? "{$this->getUserInfo()}@" : '',
			$this->getHost(),
			$this->getPort() ? ":{$this->getPort()}" : ''
		);
	}

	public function getUserInfo() : string
	{
		$username = $this->components['user'] ?? '';
		$password = $this->components['pass'] ?? '';

		return sprintf(
			'%s%s',
			$username,
			$password ? ":{$password}" : ''
		);
	}

	public function getHost() : string
	{
		return (string)($this->components['host'] ?? '');
	}

	public function getPort() : ?int
	{
		if ( isset( $this->components['port'] ) )
		{
			return (int)$this->components['port'];
		}

		return null;
	}

	public function getPath() : string
	{
		return (string)($this->components['path'] ?? '');
	}

	public function getQuery() : string
	{
		return (string)($this->components['query'] ?? '');
	}

	public function getFragment() : string
	{
		return (string)($this->components['fragment'] ?? '');
	}

	/**
	 * @param string $scheme
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public function withScheme( $scheme ) : self
	{
		$components           = $this->components;
		$components['scheme'] = (string)$scheme;

		return self::fromComponents( $components );
	}

	/**
	 * @param string $user
	 * @param null   $password
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public function withUserInfo( $user, $password = null ) : self
	{
		$components         = $this->components;
		$components['user'] = (string)$user;
		$components['pass'] = (string)$password;

		return self::fromComponents( $components );
	}

	/**
	 * @param string $host
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public function withHost( $host ) : self
	{
		$components         = $this->components;
		$components['host'] = (string)$host;

		return self::fromComponents( $components );
	}

	/**
	 * @param int|null $port
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public function withPort( $port ) : self
	{
		$components         = $this->components;
		$components['port'] = $port;

		return self::fromComponents( $components );
	}

	/**
	 * @param string $path
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public function withPath( $path ) : self
	{
		$components         = $this->components;
		$components['path'] = (string)$path;

		return self::fromComponents( $components );
	}

	/**
	 * @param string $query
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public function withQuery( $query ) : self
	{
		$components          = $this->components;
		$components['query'] = (string)$query;

		return self::fromComponents( $components );
	}

	/**
	 * @param string $fragment
	 *
	 * @return Uri
	 * @throws InvalidArgumentException
	 */
	public function withFragment( $fragment ) : self
	{
		$components             = $this->components;
		$components['fragment'] = (string)$fragment;

		return self::fromComponents( $components );
	}

	public function __toString() : string
	{
		$string = sprintf(
			'%s%s%s%s%s',
			$this->getScheme() ? "{$this->getScheme()}://" : '//',
			$this->getAuthority(),
			$this->getPath(),
			$this->getQuery() ? "?{$this->getQuery()}" : '',
			$this->getFragment() ? "#{$this->getFragment()}" : ''
		);

		return $string;
	}
}