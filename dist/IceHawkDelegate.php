<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesAppConfiguration;
use Fortuneglobe\IceHawk\TemplateEngines\Twig;

class IceHawkDelegate implements ServesAppConfiguration
{
	/**
	 * @return array
	 */
	public function getTemplateSearchPaths()
	{
		return [ __DIR__ . '/Pages' ];
	}

	/**
	 * @return string
	 */
	public function getTemplateCachePath()
	{
		return Twig::CACHE_PATH_DEFAULT;
	}

	/**
	 * @param array  $search_paths
	 * @param string $cache_path
	 *
	 * @return Twig
	 */
	public function getTemplateEngine( array $search_paths, $cache_path )
	{
		return new Twig( $search_paths, $cache_path );
	}
}