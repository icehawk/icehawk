<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Middlewares;

use IceHawk\IceHawk\Messages\Interfaces\RequestInterface;
use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Middlewares\AbstractMiddleware;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\MockObject\ReflectionException;
use PHPUnit\Framework\MockObject\RuntimeException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AbstractMiddlewareTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws Exception
	 * @throws RuntimeException
	 * @throws ReflectionException
	 */
	public function testProcessThrowsExceptionIfRequestIsNotAnIceHawkRequest() : void
	{
		$request = $this->getMockBuilder( ServerRequestInterface::class )->getMockForAbstractClass();
		$handler = $this->getMockBuilder( RequestHandlerInterface::class )->getMockForAbstractClass();

		$middleware = new class extends AbstractMiddleware {
			protected function processRequest(
				RequestInterface $request,
				RequestHandlerInterface $next
			) : ResponseInterface
			{
				return Response::new();
			}
		};

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'IceHawk middleware expects an implementation of ' . RequestInterface::class . ' as request object'
		);

		/** @var ServerRequestInterface $request */
		/** @var RequestHandlerInterface $handler */
		/** @noinspection UnusedFunctionResultInspection */
		$middleware->process( $request, $handler );
	}
}
