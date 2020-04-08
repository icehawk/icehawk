<?php declare(strict_types=1);

namespace IceHawk\IceHawk\RequestHandlers;

use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Messages\Stream;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class FallbackRequestHandler implements RequestHandlerInterface
{
	private string $message;

	private function __construct( string $message )
	{
		$this->message = $message;
	}

	public static function newWithMessage( string $message ) : self
	{
		return new self( $message );
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return ResponseInterface
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function handle( ServerRequestInterface $request ) : ResponseInterface
	{
		$message = sprintf(
			"%s\nTried to handle request for URI: %s",
			$this->message,
			(string)$request->getUri()
		);

		return Response::new()
		               ->withStatus( 404, 'Not Found' )
		               ->withHeader( 'Content-Type', 'text/plain; charset=utf-8' )
		               ->withBody( Stream::newWithContent( $message ) );
	}
}