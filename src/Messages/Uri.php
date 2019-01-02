<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use Psr\Http\Message\UriInterface;

final class Uri implements UriInterface
{
	/** @var array */
	private $components;

	private function __construct( array $components )
	{
		$this->components = $components;
	}

	public static function fromString( string $uri ) : self
	{
		return new self( parse_url( $uri ) );
	}

	public static function fromComponents( array $components ) : self
	{
		return new self( $components );
	}

	public function getScheme() : string
	{
		return $this->components['scheme'] ?? '';
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
		return $this->components['host'] ?? '';
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
		return $this->components['path'] ?? '';
	}

	public function getQuery() : string
	{
		return $this->components['query'] ?? '';
	}

	public function getFragment() : string
	{
		return $this->components['fragment'] ?? '';
	}

	public function withScheme( $scheme ) : self
	{
		$components           = $this->components;
		$components['scheme'] = (string)$scheme;

		return self::fromComponents( $components );
	}

	public function withUserInfo( $user, $password = null ) : self
	{
		$components         = $this->components;
		$components['user'] = (string)$user;
		$components['pass'] = (string)$password;

		return self::fromComponents( $components );
	}

	public function withHost( $host ) : self
	{
		$components         = $this->components;
		$components['host'] = (string)$host;

		return self::fromComponents( $components );
	}

	public function withPort( $port ) : self
	{
		$components         = $this->components;
		$components['port'] = $port;

		return self::fromComponents( $components );
	}

	public function withPath( $path ) : self
	{
		$components         = $this->components;
		$components['path'] = (string)$path;

		return self::fromComponents( $components );
	}

	public function withQuery( $query ) : self
	{
		$components          = $this->components;
		$components['query'] = (string)$query;

		return self::fromComponents( $components );
	}

	public function withFragment( $fragment ) : self
	{
		$components             = $this->components;
		$components['fragment'] = (string)$fragment;

		return self::fromComponents( $components );
	}

	public function __toString() : string
	{
		return sprintf(
			'%s%s%s%s%s',
			$this->getScheme() ? "{$this->getScheme()}://" : '//',
			$this->getAuthority(),
			$this->getPath(),
			$this->getQuery() ? "?{$this->getQuery()}" : '',
			$this->getFragment() ? "#{$this->getFragment()}" : ''
		);
	}
}