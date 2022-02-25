<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use IceHawk\IceHawk\Messages\Interfaces\RequestInterface;
use IceHawk\IceHawk\Messages\Interfaces\UploadedFilesInterface;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use UnexpectedValueException;
use function array_key_exists;
use function array_merge;
use function array_unique;
use function filter_var;
use function implode;
use function is_array;
use function is_bool;
use function is_string;
use function parse_url;
use function sprintf;
use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;
use const PHP_URL_PATH;
use const PHP_URL_QUERY;

final class Request implements RequestInterface
{
	/** @var array<string, array<string>> */
	private array $headers = [];

	/** @var array<string, mixed> */
	private array $attributes = [];

	/**
	 * @param array<string, string>                $serverParams
	 * @param array<string, mixed>                 $queryParams
	 * @param StreamInterface                      $body
	 * @param null|array<int|string, mixed>|object $parsedBody
	 * @param array<string, mixed>                 $cookieParams
	 * @param array<int|string, mixed>             $mergedParams
	 * @param UploadedFilesInterface               $uploadedFiles
	 */
	private function __construct(
		private array $serverParams,
		private array $queryParams,
		private StreamInterface $body,
		private null|array|object $parsedBody,
		private array $cookieParams,
		private array $mergedParams,
		private UploadedFilesInterface $uploadedFiles,

	)
	{
		$this->parseHeadersFromServerParams();
	}

	private function parseHeadersFromServerParams() : void
	{
		foreach ( $this->serverParams as $key => $value )
		{
			if ( !str_starts_with( $key, 'HTTP_' ) )
			{
				continue;
			}

			$headerKey = $this->parseHeaderKey( $key );

			$this->headers[ $headerKey ][] = (string)$value;
		}
	}

	private function parseHeaderKey( string $key ) : string
	{
		return str_replace( ' ', '-', ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) ) );
	}

	/**
	 * @return Request
	 * @throws InvalidArgumentException
	 */
	public static function fromGlobals() : self
	{
		return new self(
			$_SERVER,
			$_GET,
			Stream::input(),
			$_POST,
			$_COOKIE,
			$_REQUEST,
			UploadedFiles::fromGlobals(),
		);
	}

	public function getProtocolVersion() : string
	{
		return (string)($this->serverParams['SERVER_PROTOCOL'] ?? 'HTTP/1.1');
	}

	/**
	 * @param string $version
	 *
	 * @return Request
	 */
	public function withProtocolVersion( $version ) : self
	{
		$request                                  = clone $this;
		$request->serverParams['SERVER_PROTOCOL'] = (string)$version;

		return $request;
	}

	/**
	 * @return array<string, array<string>>
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
	 * @return Request
	 */
	public function withHeader( $name, $value ) : self
	{
		$request                           = clone $this;
		$request->headers[ (string)$name ] = !is_array( $value ) ? [$value] : $value;

		return $request;
	}

	/**
	 * @param string               $name
	 * @param string|array<string> $value
	 *
	 * @return Request
	 */
	public function withAddedHeader( $name, $value ) : self
	{
		$request     = clone $this;
		$headerName  = (string)$name;
		$headerValue = !is_array( $value ) ? [$value] : $value;

		$request->headers[ $headerName ] = array_unique(
			array_merge( $request->headers[ $headerName ] ?? [], $headerValue )
		);

		return $request;
	}

	/**
	 * @param string $name
	 *
	 * @return Request
	 */
	public function withoutHeader( $name ) : self
	{
		$request = clone $this;
		unset( $request->headers[ (string)$name ] );

		return $request;
	}

	public function getBody() : StreamInterface
	{
		return $this->body;
	}

	/**
	 * @param StreamInterface $body
	 *
	 * @return Request
	 */
	public function withBody( StreamInterface $body ) : self
	{
		$request       = clone $this;
		$request->body = $body;

		return $request;
	}

	public function getRequestTarget() : string
	{
		return sprintf(
			'%s%s%s',
			$this->serverParams['REQUEST_URI'] ?? '/',
			($this->serverParams['QUERY_STRING'] ?? '') ? '?' : '',
			$this->serverParams['QUERY_STRING'] ?? ''
		);
	}

	/**
	 * @param mixed $requestTarget
	 *
	 * @return Request
	 */
	public function withRequestTarget( $requestTarget ) : self
	{
		/** @var string $url */
		$url = $requestTarget;

		/** @var string $path */
		$path = parse_url( $url, PHP_URL_PATH );

		/** @var string $queryString */
		$queryString = parse_url( $url, PHP_URL_QUERY );

		$request                               = clone $this;
		$request->serverParams['REQUEST_URI']  = $path;
		$request->serverParams['QUERY_STRING'] = $queryString;

		return $request;
	}

	public function getMethod() : string
	{
		return $this->serverParams['REQUEST_METHOD'] ?? 'UNKNOWN';
	}

	/**
	 * @param string $method
	 *
	 * @return Request
	 */
	public function withMethod( $method ) : self
	{
		$request = clone $this;

		$request->serverParams['REQUEST_METHOD'] = (string)$method;

		return $request;
	}

	/**
	 * @return UriInterface
	 * @throws InvalidArgumentException
	 */
	public function getUri() : UriInterface
	{
		return Uri::fromComponents(
			[
				'scheme'   => ($this->serverParams['HTTPS'] ?? '') ? 'https' : 'http',
				'user'     => $this->serverParams['HTTP_AUTH_USER'] ?? '',
				'pass'     => $this->serverParams['HTTP_AUTH_PW'] ?? '',
				'host'     => $this->serverParams['HTTP_HOST'] ?? '',
				'port'     => $this->serverParams['SERVER_PORT'] ?? null,
				'path'     => $this->serverParams['REQUEST_URI'] ?? '',
				'query'    => $this->serverParams['QUERY_STRING'] ?? '',
				'fragment' => $this->serverParams['FRAGMENT'] ?? '',
			]
		);
	}

	/**
	 * @param UriInterface $uri
	 * @param bool         $preserveHost
	 *
	 * @return Request
	 */
	public function withUri( UriInterface $uri, $preserveHost = false ) : self
	{
		$request = clone $this;

		$request->serverParams['HTTPS'] = (string)('https' === $uri->getScheme());
		[
			$request->serverParams['HTTP_AUTH_USER'],
			$request->serverParams['HTTP_AUTH_PW'],
		] = explode( ':', $uri->getUserInfo() ) + ['', ''];

		if ( $preserveHost && empty( $request->serverParams['HTTP_HOST'] ?? '' ) && $uri->getHost() )
		{
			$request->serverParams['HTTP_HOST'] = $uri->getHost();
		}

		$request->serverParams['REQUEST_URI']  = $uri->getPath();
		$request->serverParams['QUERY_STRING'] = $uri->getQuery();
		$request->serverParams['FRAGMENT']     = $uri->getFragment();

		$request->parseHeadersFromServerParams();

		return $request;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getServerParams() : array
	{
		return $this->serverParams;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getCookieParams() : array
	{
		return $this->cookieParams;
	}

	/**
	 * @param array<string, mixed> $cookies
	 *
	 * @return Request
	 */
	public function withCookieParams( array $cookies ) : self
	{
		$request               = clone $this;
		$request->cookieParams = $cookies;

		return $request;
	}

	/**
	 * @return array<int|string, mixed>
	 */
	public function getQueryParams() : array
	{
		return $this->queryParams;
	}

	/**
	 * @param array<string, mixed> $query
	 *
	 * @return Request
	 */
	public function withQueryParams( array $query ) : self
	{
		$request               = clone $this;
		$request->queryParams  = $query;
		$request->mergedParams = array_merge( $request->mergedParams, $query );

		return $request;
	}

	/**
	 * @return array<int|string, array<int, UploadedFileInterface>>
	 */
	public function getUploadedFiles() : array
	{
		return $this->uploadedFiles->toArray();
	}

	/**
	 * @param array<string, array<int|string, UploadedFileInterface>> $uploadedFiles
	 *
	 * @return Request
	 */
	public function withUploadedFiles( array $uploadedFiles ) : self
	{
		$request                = clone $this;
		$request->uploadedFiles = UploadedFiles::fromUploadedFilesArray( $uploadedFiles );

		return $request;
	}

	/**
	 * @return array<int|string, mixed>|object|null
	 */
	public function getParsedBody() : null|array|object
	{
		return $this->parsedBody;
	}

	/**
	 * @param array<string|int, mixed>|object|null $data
	 *
	 * @return Request
	 */
	public function withParsedBody( $data ) : self
	{
		$request             = clone $this;
		$request->parsedBody = $data;

		if ( is_array( $data ) )
		{
			$request->mergedParams = array_merge( $request->mergedParams, $data );
		}

		return $request;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getAttributes() : array
	{
		return $this->attributes;
	}

	/**
	 * @param string $name
	 * @param null   $default
	 *
	 * @return mixed
	 */
	public function getAttribute( $name, $default = null ) : mixed
	{
		return $this->attributes[ (string)$name ] ?? $default;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return Request
	 */
	public function withAttribute( $name, $value ) : self
	{
		$request                              = clone $this;
		$request->attributes[ (string)$name ] = $value;

		return $request;
	}

	/**
	 * @param string $name
	 *
	 * @return Request
	 */
	public function withoutAttribute( $name ) : self
	{
		$request = clone $this;
		unset( $request->attributes[ (string)$name ] );

		return $request;
	}

	/**
	 * @param string      $key
	 * @param string|null $default
	 *
	 * @return string
	 * @throws UnexpectedValueException
	 */
	public function getInputString( string $key, ?string $default = null ) : string
	{
		$value = $this->mergedParams[ $key ] ?? $default;

		if ( !is_string( $value ) )
		{
			throw new UnexpectedValueException( sprintf( 'Input for key "%s" is not a string', $key ) );
		}

		return $value;
	}

	/**
	 * @param string                        $key
	 * @param array<int|string, mixed>|null $default
	 *
	 * @return array<int|string, mixed>
	 * @throws UnexpectedValueException
	 */
	public function getInputArray( string $key, ?array $default = null ) : array
	{
		$value = $this->mergedParams[ $key ] ?? $default;

		if ( !is_array( $value ) )
		{
			throw new UnexpectedValueException( sprintf( 'Input for key "%s" is not an array', $key ) );
		}

		return $value;
	}

	public function hasInputKey( string $key ) : bool
	{
		return array_key_exists( $key, $this->mergedParams );
	}

	#[Pure]
	public function isInputNull( string $key ) : bool
	{
		return $this->hasInputKey( $key ) && null === $this->mergedParams[ $key ];
	}

	/**
	 * @param string   $key
	 * @param int|null $default
	 *
	 * @return int
	 * @throws UnexpectedValueException
	 */
	public function getInputInt( string $key, ?int $default = null ) : int
	{
		$value    = $this->mergedParams[ $key ] ?? $default;
		$intValue = filter_var( $value, FILTER_VALIDATE_INT );

		if ( is_bool( $value ) || false === $intValue )
		{
			throw new UnexpectedValueException( sprintf( 'Input for key "%s" is not castable as integer', $key ) );
		}

		return (int)$intValue;
	}

	/**
	 * @param string     $key
	 * @param float|null $default
	 *
	 * @return float
	 * @throws UnexpectedValueException
	 */
	public function getInputFloat( string $key, ?float $default = null ) : float
	{
		$value      = $this->mergedParams[ $key ] ?? $default;
		$floatValue = filter_var( $value, FILTER_VALIDATE_FLOAT );

		if ( is_bool( $value ) || false === $floatValue )
		{
			throw new UnexpectedValueException( sprintf( 'Input for key "%s" is not castable as float', $key ) );
		}

		return (float)$floatValue;
	}

	/**
	 * @param string $name
	 * @param int    $index
	 *
	 * @return UploadedFileInterface
	 * @throws UnexpectedValueException
	 */
	public function getUploadedFile( string $name, int $index = 0 ) : UploadedFileInterface
	{
		return $this->getUploadedFiles()[ $name ][ $index ] ?? throw new UnexpectedValueException(
				sprintf( 'Could not find uploaded file for name "%s" at index "%d"', $name, $index )
			);
	}

	/**
	 * @param string $name
	 *
	 * @return UploadedFileInterface[]
	 * @throws UnexpectedValueException
	 */
	public function getUploadedFilesByName( string $name ) : array
	{
		return $this->getUploadedFiles()[ $name ] ?? throw new UnexpectedValueException(
				sprintf( 'Could not find uploaded files for name "%s"', $name )
			);
	}
}