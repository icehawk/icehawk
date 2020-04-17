<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Types;

use IceHawk\IceHawk\Types\HttpStatus;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use function fclose;
use function fgetcsv;

final class HttpStatusTest extends TestCase
{
	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testFromCode() : void
	{
		$handle = fopen( __DIR__ . '/_files/http-status-codes.csv', 'rb' );
		while ( $line = fgetcsv( $handle ) )
		{
			if ( 'Value' === $line[0] )
			{
				continue;
			}

			$httpStatus = HttpStatus::fromCode( (int)$line[0] );

			$this->assertSame( (int)$line[0], $httpStatus->getCode() );
			$this->assertSame( (string)$line[1], $httpStatus->getPhrase() );
			$this->assertSame( $line[0] . ' ' . $line[1], $httpStatus->toString() );
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
