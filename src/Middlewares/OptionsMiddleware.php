<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Middlewares;

use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function array_map;

final class OptionsMiddleware implements MiddlewareInterface
{
	private Routes $routes;

	private function __construct( Routes $routes )
	{
		$this->routes = $routes;
	}

	/**
	 * @param Routes $routes
	 *
	 * @return OptionsMiddleware
	 */
	public static function newWithRoutes( Routes $routes ) : self
	{
		return new self( $routes );
	}

	/**
	 * @param ServerRequestInterface  $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	public function process( ServerRequestInterface $request, RequestHandlerInterface $handler ) : ResponseInterface
	{
		if ( HttpMethod::options()->equalsString( $request->getMethod() ) )
		{
			return Response::new()->withStatus( 204 )->withHeader(
				'Allow',
				array_map(
					fn( HttpMethod $method ) : string => (string)$method,
					$this->routes->findAcceptedHttpMethodsForUri( $request->getUri() )
				)
			);
		}

		return $handler->handle( $request );
	}
}