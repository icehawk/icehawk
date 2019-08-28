<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use IceHawk\IceHawk\Exceptions\InvalidArgumentException;
use IceHawk\IceHawk\Exceptions\RuntimeException;
use Psr\Http\Message\StreamInterface;
use function fclose;
use function ftell;
use function is_int;
use function is_resource;
use function is_string;

final class Stream implements StreamInterface
{
	/** @var resource|false|null */
	private $resource;

	/**
	 * @param string|resource $stream
	 * @param string          $mode
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( $stream, string $mode = 'rb' )
	{
		if ( is_resource( $stream ) && 'stream' === get_resource_type( $stream ) )
		{
			$this->resource = $stream;

			return;
		}

		if ( is_string( $stream ) )
		{
			set_error_handler(
				function ()
				{
					throw new InvalidArgumentException(
						'Invalid file provided for stream; must be a valid path with valid permissions'
					);
				},
				E_WARNING
			);

			$this->resource = fopen( $stream, $mode );

			restore_error_handler();

			return;
		}

		throw new InvalidArgumentException(
			'Invalid stream provided; must be a string stream identifier or stream resource'
		);
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
		catch ( RuntimeException $e )
		{
			return '';
		}
	}

	public function close() : void
	{
		if ( !$this->resource )
		{
			return;
		}

		$resource = $this->detach();

		if ( is_resource( $resource ) )
		{
			fclose( $resource );
		}
	}

	/**
	 * @return bool|false|null|resource
	 */
	public function detach()
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

		$stats = fstat( $this->resource );

		return $stats['size'];
	}

	/**
	 * @throws RuntimeException
	 * @return int
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
	 * @throws RuntimeException
	 * @return bool
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
	 * @throws RuntimeException
	 * @return bool
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

		return is_writable( $meta['uri'] );
	}

	/**
	 * @param string $string
	 *
	 * @throws RuntimeException
	 * @return bool|int
	 */
	public function write( $string )
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

		return (false !== strpos( $mode, 'r' ) || false !== strpos( $mode, '+' ));
	}

	/**
	 * @param int $length
	 *
	 * @throws RuntimeException
	 * @return string
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

		$result = fread( $this->resource, $length );

		if ( false === $result )
		{
			throw new RuntimeException( 'Error reading stream' );
		}

		return $result;
	}

	/**
	 * @throws RuntimeException
	 * @return string
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
	 * @param null $key
	 *
	 * @return array|mixed|null
	 */
	public function getMetadata( $key = null )
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

		if ( !array_key_exists( $key, $metadata ) )
		{
			return null;
		}

		return $metadata[ $key ];
	}
}
