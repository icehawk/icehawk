<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\HttpCode;

/**
 * Class AbstractHttpResponse
 * @package Fortuneglobe\IceHawk\Responses
 */
abstract class AbstractHttpResponse
{
	/** @var string */
	private $contentType;

	/** @var int */
	private $httpCode;

	/** @var string */
	private $charset;

	public function __construct(
		string $contentType = 'text/html',
		int $httpCode = HttpCode::OK,
		string $charset = 'utf-8'
	)
	{
		$this->contentType = $contentType;
		$this->httpCode    = $httpCode;
		$this->charset     = $charset;
	}

	public function respond()
	{
		foreach ( $this->getAdditionalHeaders() as $header )
		{
			header( $header );
		}

		$body = $this->getBody();

		header( sprintf( 'Content-Type: %s; charset=%s', $this->contentType, $this->charset ), true, $this->httpCode );
		header( 'Content-Length: ' . mb_strlen( $body ) );

		echo $body;
	}

	protected function getAdditionalHeaders() : array
	{
		return [ ];
	}

	abstract protected function getBody() : string;

	/**
	 * @return string
	 */
	protected function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * @return int
	 */
	protected function getHttpCode()
	{
		return $this->httpCode;
	}

	/**
	 * @return string
	 */
	protected function getCharset()
	{
		return $this->charset;
	}
}