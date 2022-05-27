<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Middlewares;

use IceHawk\IceHawk\Messages\Interfaces\RequestInterface;
use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Routing\Interfaces\RoutesInterface;
use IceHawk\IceHawk\Types\HttpMethod;
use IceHawk\IceHawk\Types\HttpStatus;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class OptionsMiddleware extends AbstractMiddleware
{
	#[Pure]
	public static function new( RoutesInterface $routes ) : self
	{
		return new self( $routes );
	}

	private function __construct( private readonly RoutesInterface $routes ) { }

	/**
	 * @param RequestInterface        $request
	 * @param RequestHandlerInterface $next
	 *
	 * @return ResponseInterface
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	protected function processRequest( RequestInterface $request, RequestHandlerInterface $next ) : ResponseInterface
	{
		if ( HttpMethod::OPTIONS->equalsString( $request->getMethod() ) )
		{
			$acceptedHttpMethods = $this->routes->findAcceptedHttpMethodsForUri( $request->getUri() );

			return Response::new()
			               ->withStatus( HttpStatus::CODE_204->getCode() )
			               ->withHeader( 'Allow', $acceptedHttpMethods->asStringArray() );
		}

		return $next->handle( $request );
	}
}