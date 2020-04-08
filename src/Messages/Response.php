<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use function array_merge;
use function implode;
use function is_array;
use function sprintf;

class Response implements ResponseInterface
{
	private const DEFAULT_STATUS_CODE      = 200;

	private const DEFAULT_REASON_PHRASE    = 'OK';

	private const DEFAULT_PROTOCOL_VERSION = 'HTTP/1.1';

	private int $statusCode;

	private string $reasonPhrase;

	private string $protocolVersion;

	/** @var array<string, array<int, string>> */
	private array $headers;

	private StreamInterface $body;

	/**
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	private function __construct()
	{
		$this->statusCode      = self::DEFAULT_STATUS_CODE;
		$this->reasonPhrase    = self::DEFAULT_REASON_PHRASE;
		$this->protocolVersion = self::DEFAULT_PROTOCOL_VERSION;
		$this->headers         = [];
		$this->body            = Stream::newWithContent( '' );

		$this->updateStatusHeader();
	}

	/**
	 * @return static
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public static function new() : ResponseInterface
	{
		return new static();
	}

	public function getProtocolVersion() : string
	{
		return $this->protocolVersion;
	}

	/**
	 * @param string $version
	 *
	 * @return $this
	 */
	public function withProtocolVersion( $version ) : ResponseInterface
	{
		$response = clone $this;

		$response->protocolVersion = (string)$version;
		$response->updateStatusHeader();

		return $response;
	}

	/**
	 * @return array<string, array<int, string>>
	 */
	public function getHeaders() : array
	{
		return $this->headers;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasHeader( $name ) : bool
	{
		return isset( $this->headers[ (string)$name ] );
	}

	/**
	 * @param string $name
	 *
	 * @return array<int, string>
	 */
	public function getHeader( $name ) : array
	{
		return $this->headers[ (string)$name ] ?? [];
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function getHeaderLine( $name ) : string
	{
		if ( !$this->hasHeader( $name ) )
		{
			return '';
		}

		return implode( ',', $this->getHeader( $name ) );
	}

	/**
	 * @param string          $name
	 * @param string|string[] $value
	 *
	 * @return $this
	 */
	public function withHeader( $name, $value ) : ResponseInterface
	{
		$response = clone $this;

		$response->headers[ (string)$name ] = !is_array( $value ) ? [$value] : $value;

		return $response;
	}

	/**
	 * @param string          $name
	 * @param string|string[] $value
	 *
	 * @return $this
	 */
	public function withAddedHeader( $name, $value ) : ResponseInterface
	{
		$response = clone $this;

		$headerValues = is_array( $value ) ? $value : [(string)$value];

		if ( isset( $response->headers[ (string)$name ] ) )
		{
			$response->headers[ (string)$name ] = array_merge( $response->headers[ (string)$name ], $headerValues );
		}
		else
		{
			$response->headers[ (string)$name ] = $headerValues;
		}

		return $response;
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function withoutHeader( $name ) : ResponseInterface
	{
		$response = clone $this;

		unset( $response->headers[ (string)$name ] );

		return $response;
	}

	/**
	 * @return StreamInterface
	 */
	public function getBody() : StreamInterface
	{
		return $this->body;
	}

	/**
	 * @param StreamInterface $body
	 *
	 * @return $this
	 */
	public function withBody( StreamInterface $body ) : ResponseInterface
	{
		$response       = clone $this;
		$response->body = $body;

		return $response;
	}

	/**
	 * @return int
	 */
	public function getStatusCode() : int
	{
		return $this->statusCode;
	}

	/**
	 * @param int    $code
	 * @param string $reasonPhrase
	 *
	 * @return $this
	 */
	public function withStatus( $code, $reasonPhrase = '' ) : ResponseInterface
	{
		$response               = clone $this;
		$response->statusCode   = (int)$code;
		$response->reasonPhrase = (string)$reasonPhrase;

		$response->updateStatusHeader();

		return $response;
	}

	private function updateStatusHeader() : void
	{
		$this->headers['Status'] = [
			sprintf(
				'%s %d %s',
				$this->protocolVersion,
				$this->statusCode,
				$this->reasonPhrase
			),
		];
	}

	/**
	 * @return string
	 */
	public function getReasonPhrase() : string
	{
		return $this->reasonPhrase;
	}
}