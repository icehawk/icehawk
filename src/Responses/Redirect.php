<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Constants\HttpCode;

/**
 * Class Redirect
 * @package Fortuneglobe\IceHawk\Responses
 */
class Redirect extends AbstractHttpResponse
{

	/** @var string */
	private $redirectUrl;

	public function __construct( string $redirectUrl, int $httpCode = HttpCode::MOVED_PERMANENTLY )
	{
		parent::__construct( 'text/html', $httpCode );

		$this->redirectUrl = $redirectUrl;
	}

	public function respond()
	{
		session_write_close();

		parent::respond();
	}

	protected function getAdditionalHeaders() : array
	{
		return [
			'Location: ' . $this->redirectUrl,
		];
	}

	protected function getBody() : string
	{
		return sprintf(
			'<!DOCTYPE html>
			 <html lang="en"<head><meta charset="%s"><title>Redirect</title>
             <meta http-equiv="refresh" content="0;URL=\'%s\'">
             </head><body>
             <p>This page has moved to a <a href="%s">%s</a>.</p>
             </body></html>',
			strtoupper( $this->getCharset() ),
			$this->redirectUrl,
			$this->redirectUrl,
			$this->redirectUrl
		);
	}

	public function urlEquals( string $url ) : bool
	{
		return ($url == $this->redirectUrl);
	}

	public function codeEquals( int $httpCode ) : bool
	{
		return ($httpCode == $this->getHttpCode());
	}
}