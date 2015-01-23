<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

interface ServesAppConfiguration
{
	/**
	 * @return array
	 */
	public function getTemplateSearchPaths();

	/**
	 * @return string
	 */
	public function getTemplateCachePath();

	/**
	 * @param array  $search_paths
	 * @param string $cache_path
	 *
	 * @return RendersTemplate
	 */
	public function getTemplateEngine( array $search_paths, $cache_path );
}