<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Types\HttpStatus;
use InvalidArgumentException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function fclose;
use function fgetcsv;
use function is_resource;

final class HttpStatusTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws AssertionFailedError
	 */
	public function testFromCode() : void
	{
		$handle = fopen( __DIR__ . '/_files/http-status-codes.csv', 'rb' );

		if ( !is_resource( $handle ) )
		{
			self::fail( 'Could not open file handle.' );

			return;
		}

		while ( $line = fgetcsv( $handle ) )
		{
			if ( 'Value' === $line[0] )
			{
				continue;
			}

			$httpStatus = HttpStatus::fromCode( (int)$line[0] );

			self::assertSame( (int)$line[0], $httpStatus->getCode() );
			self::assertSame( (string)$line[1], $httpStatus->getPhrase() );
			self::assertSame( $line[0] . ' ' . $line[1], $httpStatus->toString() );
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
