<?php
/**
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\Responses;

use Fortuneglobe\IceHawk\Interfaces\RendersTemplate;

/**
 * Class TemplatePage
 *
 * @package Fortuneglobe\IceHawk\Responses
 */
class TemplatePage extends BaseResponse
{

	/** @var string */
	private $template;

	/** @var array */
	private $data;

	/** @var string */
	private $charset;

	/** @var RendersTemplate */
	private $templateEngine;

	/**
	 * @param string          $template
	 * @param array           $data
	 * @param string          $charset
	 * @param RendersTemplate $templateEngine
	 */
	public function __construct( $template, array $data, $charset = 'utf-8', RendersTemplate $templateEngine )
	{
		$this->template       = $template;
		$this->data           = $data;
		$this->charset        = $charset;
		$this->templateEngine = $templateEngine;
	}

	public function respond()
	{
		header( 'Content-Type: text/html; charset=' . $this->charset, true );
		echo $this->templateEngine->renderWithData( $this->template, $this->data );
	}
}