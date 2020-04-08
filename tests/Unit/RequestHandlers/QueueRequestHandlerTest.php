<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Messages\ServerRequest;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class QueueRequestHandlerTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testAdd() : void
	{
		$requestHandler = QueueRequestHandler::newWithFallbackHandler(
			FallbackRequestHandler::newWithMessage( 'Fallback active' )
		);

		$requestHandler->add(
			new class implements MiddlewareInterface {
				public function process(
					ServerRequestInterface $request,
					RequestHandlerInterface $handler
				) : ResponseInterface
				{
					return $handler->handle( $request->withAttribute( 'first', 'middleware' ) );
				}
			},
			new class implements MiddlewareInterface {
				public function process(
					ServerRequestInterface $request,
					RequestHandlerInterface $handler
				) : ResponseInterface
				{
					return $handler->handle( $request->withAttribute( 'second', 'middleware' ) );
				}
			},
			new class implements MiddlewareInterface {
				public function process(
					ServerRequestInterface $request,
					RequestHandlerInterface $handler
				) : ResponseInterface
				{
					$messages = [];
					foreach ( $request->getAttributes() as $key => $value )
					{
						$messages[] = "{$key} => {$value}";
					}

					return Response::new()->withBody( Stream::newWithContent( implode( ', ', $messages ) ) );
				}
			}
		);

		$response = $requestHandler->handle( ServerRequest::fromGlobals() );

		$expectedBody = 'first => middleware, second => middleware';

		$this->assertSame( $expectedBody, (string)$response->getBody() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testHandleUsesFallbackRequestHandlerIfNoMiddlewaresWereAdded() : void
	{
		$message        = 'Fallback active';
		$requestHandler = QueueRequestHandler::newWithFallbackHandler(
			FallbackRequestHandler::newWithMessage( $message )
		);

		$_SERVER['HTTPS'] = true;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST'] = 'example.com';
		$_SERVER['PATH_INFO'] = '/unit/test/fallback';

		$response = $requestHandler->handle( ServerRequest::fromGlobals() );

		$expectedHeaders = [
			'Status'       => [
				'HTTP/1.1 404 Not Found',
			],
			'Content-Type' => [
				'text/plain; charset=utf-8',
			],
		];

		$expectedBody = "Fallback active\nTried to handle request for URI: https://example.com/unit/test/fallback";

		$this->assertSame( 404, $response->getStatusCode() );
		$this->assertSame( 'Not Found', $response->getReasonPhrase() );
		$this->assertSame( $expectedHeaders, $response->getHeaders() );
		$this->assertSame( $expectedBody, (string)$response->getBody() );
	}
}
