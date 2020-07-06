<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\RequestHandlers;

use IceHawk\IceHawk\Exceptions\RequestHandlingFailedException;
use IceHawk\IceHawk\Messages\Request;
use IceHawk\IceHawk\RequestHandlers\FallbackRequestHandler;
use LogicException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class FallbackRequestHandlerTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 */
	public function testNewWithException() : void
	{
		$_SERVER['HTTPS'] = true;
		/** @noinspection HostnameSubstitutionInspection */
		$_SERVER['HTTP_HOST']   = 'example.com';
		$_SERVER['REQUEST_URI'] = '/unit/test/fallback';

		$request = Request::fromGlobals();

		$exception = new LogicException( 'Something is wrong.', 500 );

		try
		{
			/** @noinspection UnusedFunctionResultInspection */
			FallbackRequestHandler::newWithException( $exception )->handle( $request );
		}
		catch ( RequestHandlingFailedException $e )
		{
			$this->assertSame( 'Something is wrong.', $e->getMessage() );
			$this->assertSame( 500, $e->getCode() );
			$this->assertSame( $request, $e->getRequest() );
			$this->assertSame( $exception, $e->getPrevious() );
		}
	}
}
