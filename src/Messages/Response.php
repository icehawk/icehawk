<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use IceHawk\IceHawk\Types\HttpStatus;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;
use function array_merge;
use function implode;
use function is_array;
use function json_encode;
use const JSON_THROW_ON_ERROR;

class Response implements ResponseInterface
{
	private const DEFAULT_PROTOCOL_VERSION = 'HTTP/1.1';

	private HttpStatus $status;

	private string $protocolVersion;

	/** @var array<string, array<int, string>> */
	private array $headers;

	private StreamInterface $body;

	/**
	 * @param string $content
	 *
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	final private function __construct( string $content )
	{
		$this->status          = HttpStatus::CODE_200;
		$this->protocolVersion = self::DEFAULT_PROTOCOL_VERSION;
		$this->headers         = [];
		$this->body            = Stream::newWithContent( $content );
	}

	/**
	 * @param string $content
	 *
	 * @return static
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public static function new( string $content = '' ) : static
	{
		return new static( $content );
	}

	/**
	 * @param mixed $content
	 * @param int   $jsonFlags
	 *
	 * @return static
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 * @throws JsonException
	 */
	public static function json( mixed $content, int $jsonFlags = JSON_THROW_ON_ERROR ) : static
	{
		return static::new( (string)json_encode( $content, $jsonFlags ) )
		             ->withHeader( 'Content-Type', 'application/json' );
	}

	/**
	 * @param UriInterface $redirectUri
	 * @param int          $statusCode
	 *
	 * @return static
	 * @throws InvalidArgumentException|RuntimeException
	 */
	public static function redirect( UriInterface $redirectUri, int $statusCode = 301 ) : static
	{
		return static::new()
		             ->withStatus( $statusCode )
		             ->withHeader( 'Location', (string)$redirectUri )
		             ->withHeader( 'Content-Type', 'text/html; charset=utf-8' )
		             ->withBody(
			             Stream::newWithContent(
				             <<<EOF
							<!DOCTYPE html>
							<html lang="en">
							<head>
							   <title>Redirect $statusCode</title>
							   <meta http-equiv="refresh" content="0; 
							   url=$redirectUri">
							</head>
							<body>
							   <p>Redirecting to:
							   <a href="$redirectUri">$redirectUri</a></p>
							</body>
							</html>
							EOF
			             )
		             );
	}

	public function getProtocolVersion() : string
	{
		return $this->protocolVersion;
	}

	/**
	 * @param string $version
	 *
	 * @return static
	 */
	public function withProtocolVersion( $version ) : static
	{
		$response = clone $this;

		$response->protocolVersion = (string)$version;

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
	 * @param string               $name
	 * @param string|array<string> $value
	 *
	 * @return static
	 */
	public function withHeader( $name, $value ) : static
	{
		$response = clone $this;

		$response->headers[ (string)$name ] = !is_array( $value ) ? [$value] : $value;

		return $response;
	}

	/**
	 * @param string               $name
	 * @param string|array<string> $value
	 *
	 * @return static
	 */
	public function withAddedHeader( $name, $value ) : static
	{
		$response = clone $this;

		$headerValues = is_array( $value ) ? $value : [(string)$value];

		if ( isset( $response->headers[ (string)$name ] ) )
		{
			/** @var array<int, string> $mergedHeaders */
			$mergedHeaders = array_merge( $response->headers[ (string)$name ], $headerValues );

			$response->headers[ (string)$name ] = $mergedHeaders;
		}
		else
		{
			$response->headers[ (string)$name ] = $headerValues;
		}

		return $response;
	}

	public function withoutHeader( $name ) : static
	{
		$response = clone $this;

		unset( $response->headers[ (string)$name ] );

		return $response;
	}

	public function getBody() : StreamInterface
	{
		return $this->body;
	}

	public function withBody( StreamInterface $body ) : static
	{
		$response       = clone $this;
		$response->body = $body;

		return $response;
	}

	#[Pure]
	public function getStatusCode() : int
	{
		return $this->status->getCode();
	}

	/**
	 * @param int    $code
	 * @param string $reasonPhrase
	 *
	 * @return static
	 * @throws InvalidArgumentException
	 */
	public function withStatus( $code, $reasonPhrase = '' ) : static
	{
		$response         = clone $this;
		$response->status = HttpStatus::fromCode( (int)$code );

		return $response;
	}

	#[Pure]
	public function getReasonPhrase() : string
	{
		return $this->status->getPhrase();
	}
}