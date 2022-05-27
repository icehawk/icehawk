<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use IceHawk\IceHawk\Messages\Interfaces\StreamActionInterface;
use IceHawk\IceHawk\Types\StreamEvent;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use Throwable;
use function array_slice;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function fseek;
use function fstat;
use function ftell;
use function fwrite;
use function get_resource_type;
use function is_int;
use function is_resource;
use function restore_error_handler;
use function set_error_handler;
use function stream_get_contents;
use function stream_get_meta_data;
use const E_WARNING;

final class Stream implements StreamInterface
{
	/** @var array<StreamActionInterface> */
	private array $streamActions;

	/**
	 * @param resource|false|null $resource
	 *
	 * @throws InvalidArgumentException
	 */
	private function __construct( private mixed $resource )
	{
		$this->guardResourceIsValid();

		$this->streamActions = [];
	}

	/**
	 * @throws InvalidArgumentException
	 */
	private function guardResourceIsValid() : void
	{
		if ( !is_resource( $this->resource ) || 'stream' !== get_resource_type( $this->resource ) )
		{
			throw new InvalidArgumentException(
				'Invalid stream provided; must be a string stream identifier or stream resource'
			);
		}
	}

	/**
	 * @param string $content
	 *
	 * @return Stream
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public static function newWithContent( string $content ) : self
	{
		$stream = self::temp();
		$stream->write( $content );

		return $stream;
	}

	/**
	 * @return Stream
	 * @throws InvalidArgumentException
	 */
	public static function stdin() : self
	{
		return self::fromFile( 'php://stdin' );
	}

	/**
	 * @return Stream
	 * @throws InvalidArgumentException
	 */
	public static function stdout() : self
	{
		return self::fromFile( 'php://stdout', 'ab' );
	}

	/**
	 * @return Stream
	 * @throws InvalidArgumentException
	 */
	public static function stderr() : self
	{
		return self::fromFile( 'php://stderr', 'ab' );
	}

	/**
	 * @return Stream
	 * @throws InvalidArgumentException
	 */
	public static function temp() : self
	{
		return self::fromFile( 'php://temp', 'a+b' );
	}

	/**
	 * @return Stream
	 * @throws InvalidArgumentException
	 */
	public static function memory() : self
	{
		return self::fromFile( 'php://memory', 'a+b' );
	}

	/**
	 * @return static
	 * @throws InvalidArgumentException
	 */
	public static function input() : self
	{
		return self::fromFile( 'php://input' );
	}

	/**
	 * @return static
	 * @throws InvalidArgumentException
	 */
	public static function output() : self
	{
		return self::fromFile( 'php://output', 'ab' );
	}

	/**
	 * @param string $filepath
	 * @param string $mode
	 *
	 * @return Stream
	 * @throws InvalidArgumentException
	 */
	public static function fromFile( string $filepath, string $mode = 'rb' ) : self
	{
		set_error_handler(
			static fn() => throw new InvalidArgumentException(
				'Invalid file provided for stream; must be a valid path with valid permissions'
			),
			E_WARNING
		);

		$resource = fopen( $filepath, $mode );

		restore_error_handler();

		return new self( $resource );
	}

	/**
	 * @param resource $resource
	 *
	 * @return Stream
	 * @throws InvalidArgumentException
	 */
	public static function fromResource( mixed $resource ) : self
	{
		return new self( $resource );
	}

	public function addStreamAction( StreamActionInterface $streamAction ) : void
	{
		$this->streamActions[] = $streamAction;
	}

	/**
	 * @return string
	 */
	public function __toString() : string
	{
		if ( !$this->isReadable() )
		{
			return '';
		}

		try
		{
			$this->rewind();

			return $this->getContents();
		}
		catch ( Throwable )
		{
			return '';
		}
	}

	public function close() : void
	{
		$this->executeStreamActions( StreamEvent::CLOSING );

		if ( !$this->resource )
		{
			$this->executeStreamActions( StreamEvent::CLOSED );

			return;
		}

		$resource = $this->detach();

		if ( is_resource( $resource ) )
		{
			fclose( $resource );
		}

		$this->executeStreamActions( StreamEvent::CLOSED );
	}

	private function executeStreamActions( StreamEvent $event ) : void
	{
		foreach ( $this->streamActions as $streamAction )
		{
			if ( $event === $streamAction->getEvent() )
			{
				$streamAction->execute( $this );
			}
		}
	}

	/**
	 * @return bool|false|null|resource
	 */
	public function detach() : mixed
	{
		$resource       = $this->resource;
		$this->resource = null;

		return $resource;
	}

	/**
	 * @return int|null
	 */
	public function getSize() : ?int
	{
		if ( !is_resource( $this->resource ) )
		{
			return null;
		}

		$fstat = fstat( $this->resource );

		if ( false === $fstat )
		{
			return null;
		}

		return array_slice( $fstat, 13 )['size'];
	}

	/**
	 * @return int
	 * @throws RuntimeException
	 */
	public function tell() : int
	{
		if ( !$this->resource )
		{
			throw new RuntimeException( 'No resource available; cannot tell position' );
		}

		$result = ftell( $this->resource );

		if ( !is_int( $result ) )
		{
			throw new RuntimeException( 'Error occurred during tell operation' );
		}

		return $result;
	}

	/**
	 * @return bool
	 */
	public function eof() : bool
	{
		if ( !$this->resource )
		{
			return true;
		}

		return feof( $this->resource );
	}

	/**
	 * @return bool
	 */
	public function isSeekable() : bool
	{
		if ( !$this->resource )
		{
			return false;
		}

		$meta = stream_get_meta_data( $this->resource );

		return (bool)$meta['seekable'];
	}

	/**
	 * @param int $offset
	 * @param int $whence
	 *
	 * @return bool
	 * @throws RuntimeException
	 */
	public function seek( $offset, $whence = SEEK_SET ) : bool
	{
		if ( !$this->resource )
		{
			throw new RuntimeException( 'No resource available; cannot seek position' );
		}

		if ( !$this->isSeekable() )
		{
			throw new RuntimeException( 'Stream is not seekable' );
		}

		$result = fseek( $this->resource, $offset, $whence );

		if ( 0 !== $result )
		{
			throw new RuntimeException( 'Error seeking within stream' );
		}

		return true;
	}

	/**
	 * @return bool
	 * @throws RuntimeException
	 */
	public function rewind() : bool
	{
		return $this->seek( 0 );
	}

	/**
	 * @return bool
	 */
	public function isWritable() : bool
	{
		if ( !$this->resource )
		{
			return false;
		}

		$meta = stream_get_meta_data( $this->resource );
		$mode = $meta['mode'];

		return (
			str_contains( $mode, 'w' )
			|| str_contains( $mode, 'a' )
			|| str_contains( $mode, 'c' )
			|| str_contains( $mode, 'x' )
		);
	}

	/**
	 * @param string $string
	 *
	 * @return bool|int
	 * @throws RuntimeException
	 */
	public function write( $string ) : bool|int
	{
		if ( !$this->resource )
		{
			throw new RuntimeException( 'No resource available; cannot write' );
		}

		$result = fwrite( $this->resource, $string );

		if ( false === $result )
		{
			throw new RuntimeException( 'Error writing to stream' );
		}

		return $result;
	}

	/**
	 * @return bool
	 */
	public function isReadable() : bool
	{
		if ( !$this->resource )
		{
			return false;
		}

		$meta = stream_get_meta_data( $this->resource );
		$mode = $meta['mode'];

		return (str_contains( $mode, 'r' ) || str_contains( $mode, '+' ));
	}

	/**
	 * @param int $length
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	public function read( $length ) : string
	{
		if ( !$this->resource )
		{
			throw new RuntimeException( 'No resource available; cannot read' );
		}

		if ( !$this->isReadable() )
		{
			throw new RuntimeException( 'Stream is not readable' );
		}

		$result = fread( $this->resource, (int)max( 0, $length ) );

		if ( false === $result )
		{
			throw new RuntimeException( 'Error reading stream' );
		}

		return $result;
	}

	/**
	 * @return string
	 * @throws RuntimeException
	 */
	public function getContents() : string
	{
		if ( !is_resource( $this->resource ) || !$this->isReadable() )
		{
			return '';
		}

		$result = stream_get_contents( $this->resource );

		if ( false === $result )
		{
			throw new RuntimeException( 'Error reading from stream' );
		}

		return $result;
	}

	/**
	 * @param string|null $key
	 *
	 * @return array|mixed|null
	 */
	public function getMetadata( $key = null ) : mixed
	{
		if ( !is_resource( $this->resource ) )
		{
			return null;
		}

		if ( null === $key )
		{
			return stream_get_meta_data( $this->resource );
		}

		$metadata = stream_get_meta_data( $this->resource );

		return $metadata[ $key ] ?? null;
	}
}
