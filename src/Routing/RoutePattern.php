<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use function is_string;
use function preg_match;

final class RoutePattern
{
	private const MATCH_AGAINST_PATH     = 1;

	private const MATCH_AGAINST_FULL_URI = 2;

	private string $regexPattern;

	/** @var array<string, string> */
	private array $matches;

	private int $matchMode;

	/**
	 * @param string $regexPattern
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( string $regexPattern )
	{
		$cleanPattern = (string)preg_replace( ['#^!#', '#!(\w+)?$#'], '', $regexPattern );
		$this->guardRoutePatternIsValid( $cleanPattern );

		$this->matchMode    = self::MATCH_AGAINST_PATH;
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
		$matches     = [];
		$matchString = $this->matchMode === self::MATCH_AGAINST_FULL_URI ? (string)$uri : $uri->getPath();

		if ( !preg_match( $this->regexPattern, $matchString, $matches ) )
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

	public function matchAgainstFullUri() : void
	{
		$this->matchMode = self::MATCH_AGAINST_FULL_URI;
	}
}
