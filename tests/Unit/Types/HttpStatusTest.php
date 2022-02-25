<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use Generator;
use IceHawk\IceHawk\Types\HttpStatus;
use InvalidArgumentException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function fclose;
use function fgetcsv;
use function fopen;
use function is_resource;

final class HttpStatusTest extends TestCase
{
	/**
	 * @param int    $code
	 * @param string $expectedPhrase
	 * @param string $expectedStringRepresentation
	 *
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 *
	 * @dataProvider statusCodeProvider
	 */
	public function testFromCode( int $code, string $expectedPhrase, string $expectedStringRepresentation ) : void
	{
		$httpStatus = HttpStatus::fromCode( $code );

		self::assertSame( $code, $httpStatus->getCode() );
		self::assertSame( $expectedPhrase, $httpStatus->getPhrase() );
		self::assertSame( $expectedStringRepresentation, $httpStatus->toString() );
	}

	/**
	 * @return Generator<array<string, mixed>>
	 * @throws AssertionFailedError
	 */
	public function statusCodeProvider() : Generator
	{
		$handle = fopen( __DIR__ . '/_files/http-status-codes.csv', 'rb' );

		if ( !is_resource( $handle ) )
		{
			self::fail( 'Could not open file handle.' );
		}

		while ( $line = fgetcsv( $handle ) )
		{
			if ( 'Value' === $line[0] )
			{
				continue;
			}

			yield [
				'code'                         => (int)$line[0],
				'expectedPhrase'               => (string)$line[1],
				'expectedStringRepresentation' => $line[0] . ' ' . $line[1],
			];
		}

		fclose( $handle );
	}

	public function testFromCodeThrowsExceptionForInvalidCode() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid code for HttpStatus: 299' );

		HttpStatus::fromCode( 299 );
	}
}
