<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Routing\Interfaces\ResolvesRouteToMiddlewares;
use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class NullRoute implements Interfaces\ResolvesRouteToMiddlewares
{
	private ServerRequestInterface $modifiedRequest;

	private function __construct( ServerRequestInterface $request )
	{
		$this->modifiedRequest = $request;
	}

	public static function new( ServerRequestInterface $request ) : self
	{
		return new self( $request );
	}

	public function matchesRequest( ServerRequestInterface $request ) : bool
	{
		return false;
	}

	public function matchesUri( UriInterface $uri ) : bool
	{
		return false;
	}

	public function getMiddlewareClassNames() : MiddlewareClassNames
	{
		return MiddlewareClassNames::new();
	}

	public function getModifiedRequest() : ?ServerRequestInterface
	{
		return $this->modifiedRequest;
	}

	public function getAcceptedHttpMethods() : HttpMethods
	{
		return HttpMethods::all();
	}

	public function matchAgainstFullUri() : ResolvesRouteToMiddlewares
	{
		return $this;
	}
}