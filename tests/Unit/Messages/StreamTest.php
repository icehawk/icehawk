<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Tests\Unit\Messages;

use IceHawk\IceHawk\Messages\Stream;
use IceHawk\IceHawk\Messages\StreamAction;
use InvalidArgumentException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use function file_exists;
use function is_resource;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

final class StreamTest extends TestCase
{
	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testRead() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );
		$stream->rewind();

		self::assertSame( 'Unit-Test', $stream->read( 1024 ) );

		$stream->close();
	}

	/**
	 * @throws RuntimeException
	 */
	public function testReadThrowsExceptionWhenNoResourceIsAvailable() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot read' );

		/** @noinspection UnusedFunctionResultInspection */
		$stream->read( 1 );
	}

	/**
	 * @throws RuntimeException
	 */
	public function testReadThrowsExceptionForNonReadableStreams() : void
	{
		$nonReadableStream = new Stream( 'php://output', 'wb' );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Stream is not readable' );

		/** @noinspection UnusedFunctionResultInspection */
		$nonReadableStream->read( 1 );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testSeek() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );
		$stream->seek( 5 );

		self::assertSame( 'Test', $stream->read( 1024 ) );
	}

	/**
	 * @throws RuntimeException
	 */
	public function testSeekThrowsExceptionIfNoResourceIsAvailable() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot seek position' );

		$stream->seek( 0 );
	}

	/**
	 * @throws RuntimeException
	 */
	public function testSeekThrowsExceptionOnErrorSeekingWithinStream() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Error seeking within stream' );

		$stream->seek( 1 );
	}

	/**
	 * @throws RuntimeException
	 */
	public function testSeekThrowsExceptionForNonSeekableStreams() : void
	{
		$stream = new Stream( 'php://output', 'wb' );

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Stream is not seekable' );

		$stream->seek( 1 );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testGetSize() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		self::assertSame( 9, $stream->getSize() );

		$stream->close();

		self::assertNull( $stream->getSize() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testGetMetadata() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );

		$expectedMetaData = [
			'eof'          => false,
			'unread_bytes' => 0,
			'seekable'     => true,
			'uri'          => 'php://memory',
			'timed_out'    => false,
			'blocked'      => true,
			'wrapper_type' => 'PHP',
			'stream_type'  => 'MEMORY',
			'mode'         => 'w+b',
		];

		self::assertEquals( $expectedMetaData, $stream->getMetadata() );
		self::assertSame( 'PHP', $stream->getMetadata( 'wrapper_type' ) );
		self::assertNull( $stream->getMetadata( 'no-meta-data-key' ) );

		$stream->close();

		self::assertNull( $stream->getMetadata() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function test__toString() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		self::assertSame( 'Unit-Test', (string)$stream );
		self::assertSame( 'Unit-Test', $stream->__toString() );

		$nonReadableStream = new Stream( 'php://stdout', 'wb' );
		$nonReadableStream->write( 'Unit-Test' );

		self::assertSame( '', (string)$nonReadableStream );
		self::assertSame( '', $nonReadableStream->__toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testEof() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );
		$stream->rewind();

		self::assertFalse( $stream->eof() );

		while ( !$stream->eof() )
		{
			/** @noinspection UnusedFunctionResultInspection */
			$stream->read( 11 );
		}

		self::assertTrue( $stream->eof() );

		$stream->close();

		self::assertTrue( $stream->eof() );
	}

	/**
	 * @throws InvalidArgumentException
	 * @throws ExpectationFailedException
	 */
	public function testIsWritable() : void
	{
		$tempFile       = (string)tempnam( sys_get_temp_dir(), 'Unit-Test-OutputStream-' );
		$writableStream = new Stream( $tempFile, 'w+b' );

		self::assertTrue( $writableStream->isWritable() );

		$writableStream->close();

		self::assertFalse( $writableStream->isWritable() );

		$nonWritableStream = new Stream( 'php://input', 'rb' );

		self::assertFalse( $nonWritableStream->isWritable() );

		@unlink( $tempFile );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testRewind() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		self::assertSame( 9, $stream->tell() );

		$stream->rewind();

		self::assertSame( 0, $stream->tell() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testClose() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->close();

		self::assertNull( $stream->detach() );

		$stream->close();

		self::assertNull( $stream->detach() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testTell() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		self::assertSame( 9, $stream->tell() );

		$stream->seek( 3 );

		self::assertSame( 3, $stream->tell() );
	}

	/**
	 * @throws RuntimeException
	 */
	public function testTellThrowsExceptionIfNoResourceIsAvailable() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot tell position' );

		$stream->tell();
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testDetach() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		$resource = $stream->detach();

		self::assertIsResource( $resource );

		self::assertNull( $stream->detach() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testGetContents() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->write( 'Unit-Test' );

		$stream->seek( 5 );

		self::assertSame( 'Test', $stream->getContents() );

		$stream->rewind();

		self::assertSame( 'Unit-Test', $stream->getContents() );

		$stream->close();

		self::assertSame( '', $stream->getContents() );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testIsReadable() : void
	{
		$readableStream = new Stream( 'php://memory', 'w+b' );

		self::assertTrue( $readableStream->isReadable() );

		$readableStream->close();

		self::assertFalse( $readableStream->isReadable() );

		$nonReadableStream = new Stream( 'php://output', 'wb' );

		self::assertFalse( $nonReadableStream->isReadable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 */
	public function testWrite() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );

		self::assertSame( 9, $stream->write( 'Unit-Test' ) );
	}

	/**
	 * @throws RuntimeException
	 */
	public function testWriteThrowsExceptionIfNoResourceIsAvailable() : void
	{
		$stream = new Stream( 'php://memory', 'w+b' );
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot write' );

		$stream->write( 'Unit-Test' );
	}

	/**
	 * @throws ExpectationFailedException
	 */
	public function testIsSeekable() : void
	{
		$seekableStream = new Stream( 'php://input', 'rb' );

		self::assertTrue( $seekableStream->isSeekable() );

		$seekableStream->close();

		self::assertFalse( $seekableStream->isSeekable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws AssertionFailedError
	 */
	public function testCanConstructStreamFromResource() : void
	{
		$resource = fopen( 'php://memory', 'w+b' );

		if ( !is_resource( $resource ) )
		{
			self::fail( 'Could not open memory stream' );

			return;
		}

		$stream = new Stream( $resource );

		self::assertSame( $resource, $stream->detach() );
	}

	public function testThrowsExceptionForInvalidStream() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Invalid file provided for stream; must be a valid path with valid permissions'
		);

		new Stream( 'file:///does/not/exist' );
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testThrowsExceptionForInvalidStreamType() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Invalid stream provided; must be a string stream identifier or stream resource'
		);

		/** @noinspection PhpParamsInspection */
		new Stream( ['stream'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testNewFromFile() : void
	{
		$stream = Stream::newFromFile( __DIR__ . '/_files/StreamTest.txt' );

		self::assertSame( 'Unit-Test', $stream->getContents() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testCanAddStreamActionForClosingTheStream() : void
	{
		$tempFile = (string)tempnam( sys_get_temp_dir(), 'StreamTest_' );

		$stream = Stream::newFromFile( $tempFile, 'w+b' );
		$stream->write( 'Unit-Test' );

		$onClosingAction = StreamAction::onClosing(
			static function ( StreamInterface $stream )
			{
				echo "Stream is closing\n";
			}
		);

		$onClosedAction = StreamAction::onClosed(
			static function ( StreamInterface $stream ) use ( $tempFile )
			{
				if ( file_exists( $tempFile ) )
				{
					@unlink( $tempFile );
				}
			}
		);

		$stream->addStreamAction( $onClosingAction );
		$stream->addStreamAction( $onClosingAction );

		$stream->addStreamAction( $onClosedAction );

		self::assertFileExists( $tempFile );

		$this->expectOutputString( "Stream is closing\nStream is closing\n" );

		$stream->close();

		self::assertFileDoesNotExist( $tempFile );
	}
}
