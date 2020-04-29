<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Middlewares;

use IceHawk\IceHawk\Messages\Interfaces\ProvidesRequestData;
use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Routing\Routes;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function array_map;

final class OptionsMiddleware extends AbstractMiddleware
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
	 * @param ProvidesRequestData     $request
	 * @param RequestHandlerInterface $next
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	protected function processRequest( ProvidesRequestData $request, RequestHandlerInterface $next ) : ResponseInterface
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

		return $next->handle( $request );
	}
}