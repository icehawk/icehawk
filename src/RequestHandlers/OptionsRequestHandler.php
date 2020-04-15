<?php declare(strict_types=1);

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Routing\RouteCollection;
use IceHawk\IceHawk\Types\HttpMethod;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function array_map;

final class OptionsRequestHandler implements RequestHandlerInterface
{
	private RouteCollection $routes;

	private function __construct( RouteCollection $routes )
	{
		$this->routes = $routes;
	}

	/**
	 * @param RouteCollection $routes
	 *
	 * @return OptionsRequestHandler
	 */
	public static function newWithRoutes( RouteCollection $routes ) : self
	{
		return new self( $routes );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		$acceptedMethods = $this->routes->findAcceptedHttpMethodsForUri( $request->getUri() );

		return Response::new()->withHeader(
			'Accept',
			array_map( fn( HttpMethod $method ) : string => (string)$method, $acceptedMethods )
		);
	}
}