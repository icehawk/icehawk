<?php
/**
 *
 * @author h.woltersdorf
 */

namespace Fortuneglobe\IceHawk\TemplateEngines;

use Fortuneglobe\IceHawk\Interfaces\RendersTemplate;

/**
 * Class Twig
 *
 * @package Fortuneglobe\IceHawk\TemplateEngines
 */
final class Twig implements RendersTemplate
{

	const CACHE_PATH_DEFAULT = '/tmp/twig/cache';

	/** @var \Twig_Environment */
	private $renderer;

	/**
	 * @param array  $search_paths
	 * @param string $cache_path
	 */
	public function __construct( array $search_paths, $cache_path = self::CACHE_PATH_DEFAULT )
	{
		$this->initRenderer( $search_paths, $cache_path );
	}

	/**
	 * @param array  $search_paths
	 * @param string $cache_path
	 */
	private function initRenderer( array $search_paths, $cache_path )
	{
		$loader = new \Twig_Loader_Filesystem( $search_paths );

		$this->renderer = new \Twig_Environment(
			$loader,
			[
				'debug'      => true,
				'cache'      => $cache_path,
				'autoescape' => true,
			]
		);

		$this->renderer->addExtension( new \Twig_Extension_Debug() );
	}

	/**
	 * @param string $template
	 * @param array  $data
	 *
	 * @return string
	 */
	public function renderWithData( $template, array $data )
	{
		return $this->renderer->render( $template, $data );
	}
}
