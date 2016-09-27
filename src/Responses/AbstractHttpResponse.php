<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Responses;

use IceHawk\IceHawk\Constants\HttpCode;

/**
 * Class AbstractHttpResponse
 * @package IceHawk\IceHawk\Responses
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
		return [];
	}

	abstract protected function getBody() : string;

	protected function getContentType() : string
	{
		return $this->contentType;
	}

	protected function getHttpCode() : int
	{
		return $this->httpCode;
	}

	protected function getCharset() : string
	{
		return $this->charset;
	}
}
