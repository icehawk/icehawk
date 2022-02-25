<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use IceHawk\IceHawk\RequestHandlers\QueueRequestHandler;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use stdClass;
use Throwable;

final class QueueRequestHandlerTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RequestHandlingFailedException
	 */
	public function testAdd() : void
	{
		$requestHandler = QueueRequestHandler::new(
			FallbackRequestHandler::new( new LogicException( 'No responder found.', 404 ) )
		);

		$requestHandler->add(
			fn() => new class implements MiddlewareInterface
			{
				public function process(
					ServerRequestInterface $request,
					RequestHandlerInterface $handler
				) : ResponseInterface
				{
					return $handler->handle( $request->withAttribute( 'first', 'middleware' ) );
				}
			},
			fn() => new class implements MiddlewareInterface
			{
				public function process(
					ServerRequestInterface $request,
					RequestHandlerInterface $handler
				) : ResponseInterface
				{
					return $handler->handle( $request->withAttribute( 'second', 'middleware' ) );
				}
			},
			fn() => new class implements MiddlewareInterface
			{
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

		$response = $requestHandler->handle( Request::fromGlobals() );

		$expectedBody = 'first => middleware, second => middleware';

		self::assertSame( $expectedBody, (string)$response->getBody() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testHandleUsesFallbackRequestHandlerIfNoMiddlewaresWereAdded() : void
	{
		$exception      = new LogicException( 'No responder found.', 404 );
		$requestHandler = QueueRequestHandler::new(
			FallbackRequestHandler::new( $exception )
		);

		$_SERVER['HTTPS'] = true;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test/fallback';

		$request = Request::fromGlobals();

		try
		{
			/** @noinspection UnusedFunctionResultInspection */
			$requestHandler->handle( $request );
		}
		catch ( Throwable $e )
		{
			self::assertInstanceOf( RequestHandlingFailedException::class, $e );
			self::assertSame( 'No responder found.', $e->getMessage() );
			self::assertSame( 404, $e->getCode() );

			/** @var RequestHandlingFailedException $e */
			self::assertSame( $request, $e->getRequest() );
			self::assertSame( $exception, $e->getPrevious() );
		}
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testHandleThrowsExceptionIfMiddlewareDoesNotImplementMiddlewareInterface() : void
	{
		$exception      = new LogicException( 'No responder found.', 404 );
		$requestHandler = QueueRequestHandler::new(
			FallbackRequestHandler::new( $exception )
		);

		$requestHandler->add( fn() => new stdClass() );

		$_SERVER['HTTPS'] = true;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test/fallback';

		$request = Request::fromGlobals();

		$this->expectException( RequestHandlingFailedException::class );
		$this->expectExceptionMessage(
			'Instance did not resolve to a class implementing ' . MiddlewareInterface::class
		);

		/** @noinspection UnusedFunctionResultInspection */
		$requestHandler->handle( $request );
	}
}
