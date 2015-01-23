<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

/**
 * Interface RendersTemplate
 *
 * @package Fortuneglobe\IceHawk\Interfaces
 */
interface RendersTemplate
{
	/**
	 * @param string $template
	 * @param array  $data
	 *
	 * @return mixed
	 */
	public function renderWithData( $template, array $data );
}