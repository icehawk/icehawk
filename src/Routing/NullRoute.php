<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Interfaces\HttpMethodsInterface;
use IceHawk\IceHawk\Interfaces\MiddlewareClassNamesInterface;
use IceHawk\IceHawk\Routing\Interfaces\RouteInterface;
use IceHawk\IceHawk\Types\HttpMethods;
use IceHawk\IceHawk\Types\MiddlewareClassNames;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class NullRoute implements Interfaces\RouteInterface
{
	#[Pure]
	public static function new( ServerRequestInterface $request ) : self
	{
		return new self( $request );
	}

	private function __construct( private readonly ServerRequestInterface $request ) { }

	public function matchesRequest( ServerRequestInterface $request ) : bool
	{
		return false;
	}

	public function matchesUri( UriInterface $uri ) : bool
	{
		return false;
	}

	#[Pure]
	public function getMiddlewareClassNames() : MiddlewareClassNamesInterface
	{
		return MiddlewareClassNames::new();
	}

	public function getModifiedRequest() : ?ServerRequestInterface
	{
		return $this->request;
	}

	public function getAcceptedHttpMethods() : HttpMethodsInterface
	{
		return HttpMethods::all();
	}

	public function matchAgainstFullUri() : RouteInterface
	{
		return $this;
	}
}