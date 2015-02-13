<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\IceHawk;

class Page extends BaseResponse
{

	/** @var string */
	private $template;

	/** @var array */
	private $data;

	/** @var string */
	private $charset;

	/**
	 * @param string $template
	 * @param array  $data
	 * @param string $charset
	 */
	public function __construct( $template, array $data, $charset = 'utf-8' )
	{
		$this->template = $template;
		$this->data     = $data;
		$this->charset = $charset;
	}

	public function respond()
	{
		$templateEngine = IceHawk::fromSky()->getTemplateEngine();

		header( 'Content-Type: text/html; charset=' . $this->charset, true );
		echo $templateEngine->renderWithData( $this->template, $this->data );
	}
}