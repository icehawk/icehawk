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
	 * @throws InvalidArgumentException
	 */
	public function testRead() : void
	{
		$stream = Stream::memory();
		$stream->write( 'Unit-Test' );
		$stream->rewind();

		self::assertSame( 'Unit-Test', $stream->read( 1024 ) );

		$stream->close();
	}

	/**
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testReadThrowsExceptionWhenNoResourceIsAvailable() : void
	{
		$stream = Stream::memory();
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot read' );

		/** @noinspection UnusedFunctionResultInspection */
		$stream->read( 1 );
	}

	/**
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testReadThrowsExceptionForNonReadableStreams() : void
	{
		$nonReadableStream = Stream::output();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Stream is not readable' );

		/** @noinspection UnusedFunctionResultInspection */
		$nonReadableStream->read( 1 );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testSeek() : void
	{
		$stream = Stream::memory();
		$stream->write( 'Unit-Test' );
		$stream->seek( 5 );

		self::assertSame( 'Test', $stream->read( 1024 ) );
	}

	/**
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testSeekThrowsExceptionIfNoResourceIsAvailable() : void
	{
		$stream = Stream::memory();
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot seek position' );

		$stream->seek( 0 );
	}

	/**
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testSeekThrowsExceptionOnErrorSeekingWithinStream() : void
	{
		$stream = Stream::memory();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Error seeking within stream' );

		$stream->seek( 1 );
	}

	/**
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testSeekThrowsExceptionForNonSeekableStreams() : void
	{
		$stream = Stream::output();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'Stream is not seekable' );

		$stream->seek( 1 );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testGetSize() : void
	{
		$stream = Stream::memory();
		$stream->write( 'Unit-Test' );

		self::assertSame( 9, $stream->getSize() );

		$stream->close();

		self::assertNull( $stream->getSize() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testGetMetadata() : void
	{
		$stream = Stream::memory();

		$expectedMetaData = [
			'eof'          => false,
			'unread_bytes' => 0,
			'seekable'     => true,
			'uri'          => 'php://memory',
			'timed_out'    => false,
			'blocked'      => true,
			'wrapper_type' => 'PHP',
			'stream_type'  => 'MEMORY',
			'mode'         => 'a+b',
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
	 * @throws InvalidArgumentException
	 */
	public function test__toString() : void
	{
		$stream = Stream::memory();
		$stream->write( 'Unit-Test' );

		self::assertSame( 'Unit-Test', (string)$stream );
		self::assertSame( 'Unit-Test', $stream->__toString() );

		$nonReadableStream = Stream::stdout();
		$nonReadableStream->write( 'Unit-Test' );

		self::assertSame( '', (string)$nonReadableStream );
		self::assertSame( '', $nonReadableStream->__toString() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testEof() : void
	{
		$stream = Stream::memory();
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
		$writableStream = Stream::fromFile( $tempFile, 'w+b' );

		self::assertTrue( $writableStream->isWritable() );

		$writableStream->close();

		self::assertFalse( $writableStream->isWritable() );

		$nonWritableStream = Stream::input();

		self::assertFalse( $nonWritableStream->isWritable() );

		@unlink( $tempFile );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testRewind() : void
	{
		$stream = Stream::memory();
		$stream->write( 'Unit-Test' );

		self::assertSame( 9, $stream->tell() );

		$stream->rewind();

		self::assertSame( 0, $stream->tell() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testClose() : void
	{
		$stream = Stream::memory();
		$stream->close();

		self::assertNull( $stream->detach() );

		$stream->close();

		self::assertNull( $stream->detach() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testTell() : void
	{
		$stream = Stream::memory();
		$stream->write( 'Unit-Test' );

		self::assertSame( 9, $stream->tell() );

		$stream->seek( 3 );

		self::assertSame( 3, $stream->tell() );
	}

	/**
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testTellThrowsExceptionIfNoResourceIsAvailable() : void
	{
		$stream = Stream::memory();
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot tell position' );

		$stream->tell();
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testDetach() : void
	{
		$stream = Stream::memory();
		$stream->write( 'Unit-Test' );

		$resource = $stream->detach();

		self::assertIsResource( $resource );

		self::assertNull( $stream->detach() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testGetContents() : void
	{
		$stream = Stream::memory();
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
	 * @throws InvalidArgumentException
	 */
	public function testIsReadable() : void
	{
		$readableStream = Stream::memory();

		self::assertTrue( $readableStream->isReadable() );

		$readableStream->close();

		self::assertFalse( $readableStream->isReadable() );

		$nonReadableStream = Stream::output();

		self::assertFalse( $nonReadableStream->isReadable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testWrite() : void
	{
		$stream = Stream::memory();

		self::assertSame( 9, $stream->write( 'Unit-Test' ) );
	}

	/**
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function testWriteThrowsExceptionIfNoResourceIsAvailable() : void
	{
		$stream = Stream::memory();
		$stream->close();

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( 'No resource available; cannot write' );

		$stream->write( 'Unit-Test' );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testIsSeekable() : void
	{
		$seekableStream = Stream::input();

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
		}

		$stream = Stream::fromResource( $resource );

		self::assertSame( $resource, $stream->detach() );
	}

	public function testFromFileThrowsExceptionForInvalidFile() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Invalid file provided for stream; must be a valid path with valid permissions'
		);

		Stream::fromFile( 'file:///does/not/exist' );
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function testFromResourceThrowsExceptionForInvalidStreamType() : void
	{
		$this->expectException( InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Invalid stream provided; must be a string stream identifier or stream resource'
		);

		Stream::fromResource( ['stream'] );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function testNewFromFile() : void
	{
		$stream = Stream::fromFile( __DIR__ . '/_files/StreamTest.txt' );

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

		$stream = Stream::fromFile( $tempFile, 'w+b' );
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

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testStdin() : void
	{
		self::assertTrue( Stream::stdin()->isReadable() );
		self::assertFalse( Stream::stdin()->isWritable() );
		self::assertFalse( Stream::stdin()->isSeekable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testStdout() : void
	{
		self::assertFalse( Stream::stdout()->isReadable() );
		self::assertTrue( Stream::stdout()->isWritable() );
		self::assertFalse( Stream::stdout()->isSeekable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testStderr() : void
	{
		self::assertFalse( Stream::stderr()->isReadable() );
		self::assertTrue( Stream::stderr()->isWritable() );
		self::assertFalse( Stream::stderr()->isSeekable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testTemp() : void
	{
		self::assertTrue( Stream::temp()->isReadable() );
		self::assertTrue( Stream::temp()->isWritable() );
		self::assertTrue( Stream::temp()->isSeekable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testMemory() : void
	{
		self::assertTrue( Stream::memory()->isReadable() );
		self::assertTrue( Stream::memory()->isWritable() );
		self::assertTrue( Stream::memory()->isSeekable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testInput() : void
	{
		self::assertTrue( Stream::input()->isReadable() );
		self::assertFalse( Stream::input()->isWritable() );
		self::assertTrue( Stream::input()->isSeekable() );
	}

	/**
	 * @throws ExpectationFailedException
	 * @throws InvalidArgumentException
	 */
	public function testOutput() : void
	{
		self::assertFalse( Stream::output()->isReadable() );
		self::assertTrue( Stream::output()->isWritable() );
		self::assertFalse( Stream::output()->isSeekable() );
	}
}
