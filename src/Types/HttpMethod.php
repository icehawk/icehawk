<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Types;

use function array_values;
use function strtoupper;

enum HttpMethod: string
{
	case GET = 'GET';

	case HEAD = 'HEAD';

	case POST = 'POST';

	case PUT = 'PUT';

	case PATCH = 'PATCH';

	case DELETE = 'DELETE';

	case OPTIONS = 'OPTIONS';

	case CONNECT = 'CONNECT';

	case TRACE = 'TRACE';

	public function toString() : string
	{
		return $this->value;
	}

	public function equalsOneOf( HttpMethod $other, HttpMethod ...$others ) : bool
	{
		return in_array( $this, [$other, ...array_values( $others )], true );
	}

	public function equalsString( string $otherMethod ) : bool
	{
		return self::tryFrom( strtoupper( $otherMethod ) ) === $this;
	}
}
