<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use IceHawk\IceHawk\Messages\Interfaces\ProvidesRequestData;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use UnexpectedValueException;
use function array_key_exists;
use function implode;
use function is_array;
use function is_string;
use function parse_url;
use function sprintf;
use const PHP_URL_PATH;
use const PHP_URL_QUERY;

final class Request implements ProvidesRequestData
{
	/** @var array<string,mixed> */
	private array $serverParams;

	/** @var array<string,array> */
	private array $headers;

	/** @var array<string,mixed> */
	private array $queryParams;

	private StreamInterface $body;

	/** @var null|array<mixed>|object */
	private $parsedBody;

	/** @var array<string,mixed> */
	private array $cookieParams;

	/** @var array<string,mixed> */
	private array $mergedParams;

	private UploadedFilesCollection $uploadedFiles;

	/** @var array<string,mixed> */
	private array $attributes;

	/**
	 * @param array<string,mixed>      $serverParams
	 * @param array<string,mixed>      $queryParams
	 * @param StreamInterface          $body
	 * @param null|array<mixed>|object $parsedBody
	 * @param array<string,mixed>      $cookieParams
	 * @param array<string,mixed>      $mergedParams
	 * @param UploadedFilesCollection  $uploadedFiles
	 * @param array<string,mixed>      $attributes
	 */
	private function __construct(
		array $serverParams,
		array $queryParams,
		StreamInterface $body,
		$parsedBody,
		array $cookieParams,
		array $mergedParams,
		UploadedFilesCollection $uploadedFiles,
		array $attributes
	)
	{
		$this->serverParams  = $serverParams;
		$this->queryParams   = $queryParams;
		$this->body          = $body;
		$this->parsedBody    = $parsedBody;
		$this->cookieParams  = $cookieParams;
		$this->mergedParams  = $mergedParams;
		$this->uploadedFiles = $uploadedFiles;
		$this->attributes    = $attributes;

		$this->parseHeadersFromServerParams();
	}

	private function parseHeadersFromServerParams() : void
	{
		foreach ( $this->serverParams as $key => $value )
		{
			if ( strpos( $key, 'HTTP_' ) !== 0 )
			{
				continue;
			}

			$headerKey = $this->parseHeaderKey( $key );

			$this->headers[ $headerKey ][] = $value;
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
			$_SERVER ?? [],
			$_GET ?? [],
			new Stream( 'php://input', 'rb' ),
			$_POST ?? [],
			$_COOKIE ?? [],
			$_REQUEST ?? [],
			UploadedFilesCollection::fromGlobals(),
			[]
		);
	}

	public function getProtocolVersion() : string
	{
		return $this->serverParams['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
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
	 * @return array<string,array>
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
	 * @return array<int,string>
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
		$request = clone $this;

		if ( $request->hasHeader( $name ) )
		{
			$request->headers[ (string)$name ] = array_merge(
				$request->headers[ (string)$name ],
				!is_array( $value ) ? [$value] : $value
			);
		}

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
		$url         = (string)$requestTarget;
		$path        = parse_url( $url, PHP_URL_PATH );
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
				'path'     => $this->serverParams['PATH_INFO'] ?? '',
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
		$request                        = clone $this;
		$request->serverParams['HTTPS'] = 'https' === $uri->getScheme();
		[
			$request->serverParams['HTTP_AUTH_USER'],
			$request->serverParams['HTTP_AUTH_PW'],
		] = explode( ':', $uri->getUserInfo() );

		if ( $preserveHost && $uri->getHost() )
		{
			$request->serverParams['HTTP_HOST'] = $uri->getHost();
		}

		$request->serverParams['PATH_INFO']    = $uri->getPath();
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
	 * @return array<string, mixed>
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
		$request              = clone $this;
		$request->queryParams = $query;

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
	 * @param array<string, array<int,UploadedFileInterface>> $uploadedFiles
	 *
	 * @return Request
	 */
	public function withUploadedFiles( array $uploadedFiles ) : self
	{
		$request                = clone $this;
		$request->uploadedFiles = UploadedFilesCollection::fromUploadedFilesArray( $uploadedFiles );

		return $request;
	}

	/**
	 * @return array<mixed>|object|null
	 */
	public function getParsedBody()
	{
		return $this->parsedBody;
	}

	/**
	 * @param array<mixed>|object|null $data
	 *
	 * @return Request
	 */
	public function withParsedBody( $data ) : self
	{
		$request             = clone $this;
		$request->parsedBody = $data;

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
	 * @return mixed|null
	 */
	public function getAttribute( $name, $default = null )
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
	 * @param string            $key
	 * @param array<mixed>|null $default
	 *
	 * @return array<mixed>
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
		$value    = $this->mergedParams[ $key ] ?? (string)$default;
		$intValue = (int)$value;

		if ( (string)$intValue !== $value )
		{
			throw new UnexpectedValueException( sprintf( 'Input for key "%s" is not castable as integer', $key ) );
		}

		return $intValue;
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
		$value      = $this->mergedParams[ $key ] ?? (string)$default;
		$floatValue = (float)$value;

		if ( (string)$floatValue !== $value )
		{
			throw new UnexpectedValueException( sprintf( 'Input for key "%s" is not castable as float', $key ) );
		}

		return $floatValue;
	}
}