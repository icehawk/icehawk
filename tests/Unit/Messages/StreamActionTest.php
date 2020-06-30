<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Messages\StreamAction;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

final class StreamActionTest extends TestCase
{
	public function testThrowsExceptionForInvalidEventName() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Invalid event name for stream action: onInvalid' );

		/** @noinspection UnusedFunctionResultInspection */
		StreamAction::new( 'onInvalid', fn() => false );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testOnClosing() : void
	{
		$action = StreamAction::onClosing(
			static function ( StreamInterface $stream )
			{
				echo $stream;
			}
		);

		$this->assertSame( 'onClosing', $action->getEventName() );

		$this->expectOutputString( 'Unit-Test' );

		$action->execute( Stream::newWithContent( 'Unit-Test' ) );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testOnClosed() : void
	{
		$action = StreamAction::onClosed(
			static function ( StreamInterface $stream )
			{
				echo $stream;
			}
		);

		$this->assertSame( 'onClosed', $action->getEventName() );

		$this->expectOutputString( 'Unit-Test' );

		$action->execute( Stream::newWithContent( 'Unit-Test' ) );
	}
}
