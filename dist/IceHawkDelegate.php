<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ResolvesUri;
use Fortuneglobe\IceHawk\Interfaces\RewritesUri;
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

	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter()
	{
		return new UriRewriter();
	}

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver()
	{
		return new UriResolver();
	}

	/**
	 * @return string
	 */
	public function getProjectNamespace()
	{
		return __NAMESPACE__;
	}
}