<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Messages\StreamAction;
use IceHawk\IceHawk\Types\StreamEvent;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

final class StreamActionTest extends TestCase
{
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

		self::assertSame( StreamEvent::CLOSING, $action->getEvent() );

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

		self::assertSame( StreamEvent::CLOSED, $action->getEvent() );

		$this->expectOutputString( 'Unit-Test' );

		$action->execute( Stream::newWithContent( 'Unit-Test' ) );
	}
}
