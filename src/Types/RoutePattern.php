<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use function is_string;
use function preg_match;

final class RoutePattern
{
	private string $regexPattern;

	/** @var array<string, string> */
	private array $matches;

	/**
	 * @param string $regexPattern
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( string $regexPattern )
	{
		$cleanPattern = (string)preg_replace( ['#^!#', '#!i?#'], '', $regexPattern );
		$this->guardRoutePatternIsValid( $cleanPattern );

		$this->regexPattern = '!' . $cleanPattern . '!i';
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
	 *
	 * @return RoutePattern
	 * @throws InvalidArgumentException
	 */
	public static function newFromString( string $regexPattern ) : self
	{
		return new self( $regexPattern );
	}

	public function matchesUri( UriInterface $uri ) : bool
	{
		$matches = [];
		$result  = (bool)preg_match( $this->regexPattern, (string)$uri, $matches );

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
		return $this->regexPattern;
	}
}
