<?php declare(strict_types=1);

namespace IceHawk\IceHawk\Messages;

use IceHawk\IceHawk\Exceptions\InvalidArgumentException;
use IceHawk\IceHawk\Exceptions\RuntimeException;
use IceHawk\IceHawk\Messages\Interfaces\ProvidesRequestData;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use function implode;
use function is_array;
use function is_string;
use function parse_url;
use const PHP_URL_PATH;
use const PHP_URL_QUERY;

final class ServerRequest implements ProvidesRequestData
{
	/** @var array */
	private $serverParams;

	/** @var array */
	private $headers;

	/** @var array */
	private $queryParams;

	/** @var StreamInterface */
	private $body;

	/** @var null|array|object */
	private $parsedBody;

	/** @var array */
	private $cookieParams;

	/** @var array */
	private $mergedParams;

	/** @var array */
	private $uploadedFiles;

	/** @var array */
	private $attributes;

	/**
	 * @param array             $serverParams
	 * @param array             $queryParams
	 * @param StreamInterface   $body
	 * @param null|array|object $parsedBody
	 * @param array             $cookieParams
	 * @param array             $mergedParams
	 * @param array             $uploadedFiles
	 * @param array             $attributes
	 */
	private function __construct(
		array $serverParams,
		array $queryParams,
		StreamInterface $body,
		$parsedBody,
		array $cookieParams,
		array $mergedParams,
		array $uploadedFiles,
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
		return str_replace(
			' ',
			'-',
			ucwords(
				str_replace(
					'_',
					' ',
					strtolower( substr( $key, 5 ) )
				)
			)
		);
	}

	/**
	 * @throws InvalidArgumentException
	 * @return ServerRequest
	 */
	public static function fromGlobals() : self
	{
		return new self(
			$_SERVER ?? [],
			$_GET ?? [],
			new Stream( 'php://input', 'rb' ),
			$_POST ?? [],
			$_COOKIE ?? [],
			$_REQUEST,
			$_FILES ?? [],
			[]
		);
	}

	public function getProtocolVersion() : string
	{
		return $this->serverParams['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
	}

	public function withProtocolVersion( $version ) : self
	{
		$request                                  = clone $this;
		$request->serverParams['SERVER_PROTOCOL'] = (string)$version;

		return $request;
	}

	public function getHeaders() : array
	{
		return $this->headers;
	}

	public function hasHeader( $name ) : bool
	{
		return isset( $this->headers[ (string)$name ] );
	}

	public function getHeader( $name )
	{
		return $this->headers[ (string)$name ] ?? [];
	}

	public function getHeaderLine( $name ) : string
	{
		if ( !$this->hasHeader( $name ) )
		{
			return '';
		}

		return implode( ',', $this->getHeader( $name ) );
	}

	public function withHeader( $name, $value ) : self
	{
		$request                           = clone $this;
		$request->headers[ (string)$name ] = !is_array( $value ) ? [$value] : $value;

		return $request;
	}

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

	public function withMethod( $method ) : self
	{
		$request = clone $this;

		$request->serverParams['REQUEST_METHOD'] = (string)$method;

		return $request;
	}

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

	public function getServerParams() : array
	{
		return $this->serverParams;
	}

	public function getCookieParams() : array
	{
		return $this->cookieParams;
	}

	public function withCookieParams( array $cookies ) : self
	{
		$request               = clone $this;
		$request->cookieParams = $cookies;

		return $request;
	}

	public function getQueryParams() : array
	{
		return $this->queryParams;
	}

	public function withQueryParams( array $query ) : self
	{
		$request              = clone $this;
		$request->queryParams = $query;

		return $request;
	}

	public function getUploadedFiles() : array
	{
		return $this->uploadedFiles;
	}

	public function withUploadedFiles( array $uploadedFiles ) : self
	{
		$request                = clone $this;
		$request->uploadedFiles = $uploadedFiles;

		return $request;
	}

	public function getParsedBody()
	{
		return $this->parsedBody;
	}

	public function withParsedBody( $data )
	{
		$request             = clone $this;
		$request->parsedBody = $data;

		return $request;
	}

	public function getAttributes() : array
	{
		return $this->attributes;
	}

	public function getAttribute( $name, $default = null )
	{
		return $this->attributes[ (string)$name ] ?? $default;
	}

	public function withAttribute( $name, $value ) : self
	{
		$request                              = clone $this;
		$request->attributes[ (string)$name ] = $value;

		return $request;
	}

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
	 * @throws RuntimeException
	 * @return string
	 */
	public function getInputString( string $key, ?string $default = null ) : string
	{
		$value = $this->mergedParams[ $key ] ?? $default;

		if ( !is_string( $value ) )
		{
			throw new RuntimeException(
				sprintf(
					'Input for key "%s" is not a string',
					$key
				)
			);
		}

		return $value;
	}

	/**
	 * @param string     $key
	 * @param array|null $default
	 *
	 * @throws RuntimeException
	 * @return array
	 */
	public function getInputArray( string $key, ?array $default = null ) : array
	{
		$value = $this->mergedParams[ $key ] ?? $default;

		if ( !is_array( $value ) )
		{
			throw new RuntimeException(
				sprintf(
					'Input for key "%s" is not an array',
					$key
				)
			);
		}

		return $value;
	}
}