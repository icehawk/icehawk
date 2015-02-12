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

	/**
	 * @param       $template
	 * @param array $data
	 */
	public function __construct( $template, array $data )
	{
		$this->template = $template;
		$this->data     = $data;
	}

	public function respond()
	{
		$templateEngine = IceHawk::fromSky()->getTemplateEngine();

		header( 'Content-Type: text/html; charset=utf-8' );
		echo $templateEngine->renderWithData( $this->template, $this->data );
	}
}