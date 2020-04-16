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

	private RequestHandlerInterface $requestHandler;

	private function __construct( RouteCollection $routes, RequestHandlerInterface $requestHandler )
	{
		$this->routes         = $routes;
		$this->requestHandler = $requestHandler;
	}

	/**
	 * @param RouteCollection         $routes
	 * @param RequestHandlerInterface $requestHandler
	 *
	 * @return OptionsRequestHandler
	 */
	public static function new( RouteCollection $routes, RequestHandlerInterface $requestHandler ) : self
	{
		return new self( $routes, $requestHandler );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		if ( !HttpMethod::options()->equalsString( $request->getMethod() ) )
		{
			return $this->requestHandler->handle( $request );
		}

		$acceptedMethods = $this->routes->findAcceptedHttpMethodsForUri( $request->getUri() );

		return Response::new()
		               ->withStatus( 204, 'No Content' )
		               ->withHeader(
			               'Allow',
			               array_map( fn( HttpMethod $method ) : string => (string)$method, $acceptedMethods )
		               );
	}
}