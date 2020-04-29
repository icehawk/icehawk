<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Middlewares;

use IceHawk\IceHawk\Messages\Interfaces\ProvidesRequestData;
use IceHawk\IceHawk\Messages\Response;
use IceHawk\IceHawk\Middlewares\AbstractMiddleware;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
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
	 */
	public function testProcessThrowsExceptionIfRequestIsNotAnIceHawkRequest() : void
	{
		$request = $this->getMockBuilder( ServerRequestInterface::class )->getMockForAbstractClass();
		$handler = $this->getMockBuilder( RequestHandlerInterface::class )->getMockForAbstractClass();

		$middleware = new class extends AbstractMiddleware {
			protected function processRequest(
				ProvidesRequestData $request,
				RequestHandlerInterface $next
			) : ResponseInterface
			{
				return Response::new();
			}
		};

		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'IceHawk middleware expects an implementation of ' . ProvidesRequestData::class . ' as request object'
		);

		/** @var ServerRequestInterface $request */
		/** @var RequestHandlerInterface $handler */
		/** @noinspection UnusedFunctionResultInspection */
		$middleware->process( $request, $handler );
	}
}
