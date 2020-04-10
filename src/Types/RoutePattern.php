<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use function is_string;
use function preg_match;

final class RoutePattern
{
	private string $regexPattern;

	private string $flags;

	/** @var array<string, string> */
	private array $matches;

	/**
	 * @param string $regexPattern
	 * @param string $flags
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( string $regexPattern, string $flags = '' )
	{
		$this->guardRoutePatternIsValid( $regexPattern );

		$this->regexPattern = '!' . trim( $regexPattern, '!' ) . '!';
		$this->flags        = $flags;
		$this->matches      = [];
	}

	/**
	 * @param string $regexPattern
	 *
	 * @throws InvalidArgumentException
	 */
	private function guardRoutePatternIsValid( string $regexPattern ) : void
	{
		if ( '' === trim( $regexPattern ) )
		{
			throw new InvalidArgumentException( 'Invalid value for RoutePattern: empty' );
		}
	}

	/**
	 * @param string $regexPattern
	 * @param string $flags
	 *
	 * @return RoutePattern
	 * @throws InvalidArgumentException
	 */
	public static function newFromString( string $regexPattern, string $flags = '' ) : self
	{
		return new self( $regexPattern, $flags );
	}

	public function matchesUri( UriInterface $uri ) : bool
	{
		$matches = [];
		$result  = (bool)preg_match( $this->toString(), (string)$uri, $matches );

		if ( !$result )
		{
			return false;
		}

		foreach ( $matches as $key => $value )
		{
			if ( is_string( $key ) )
			{
				$this->matches[ $key ] = $value;
			}
		}

		return true;
	}

	/**
	 * @return array<string, string>
	 */
	public function getMatches() : array
	{
		return $this->matches;
	}

	public function toString() : string
	{
		return $this->regexPattern . $this->flags;
	}
}
