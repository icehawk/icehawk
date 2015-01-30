<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Interfaces\ServesAppConfiguration;

final class IceHawk
{

	/** @var ServesAppConfiguration */
	private $config_delegate;

	/** @var Interfaces\RendersTemplate */
	private $template_engine;

	/**
	 * @return IceHawk
	 */
	public static function fromSky()
	{
		static $instance = null;

		if ( is_null( $instance ) )
		{
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * @param ServesAppConfiguration $config_delegate
	 */
	public function init( ServesAppConfiguration $config_delegate = null )
	{
		if ( is_null( $config_delegate ) )
		{
			$this->config_delegate = new IceHawkDelegate();
		}
		else
		{
			$this->config_delegate = $config_delegate;
		}
	}

	/**
	 * @return Interfaces\RendersTemplate
	 */
	public function getTemplateEngine()
	{
		$this->initTemplateEngineIfNeeded();

		return $this->template_engine;
	}

	private function initTemplateEngineIfNeeded()
	{
		if ( is_null( $this->template_engine ) )
		{
			$search_paths = $this->config_delegate->getTemplateSearchPaths();
			$cache_path   = $this->config_delegate->getTemplateCachePath();

			$this->template_engine = $this->config_delegate->getTemplateEngine( $search_paths, $cache_path );
		}
	}

	/**
	 * @return RequestHandler
	 */
	public function getRequestHandler()
	{
		$request_info = RequestInfo::fromEnv();

		$request_handler_delegate = new RequestHandlerDelegate(
			$this->getUriRewriter(),
			$this->getUriResolver(),
			$this->getProjectNamespace()
		);

		return new RequestHandler( $request_info, $request_handler_delegate, $_GET, $_POST, $_FILES );
	}

	/**
	 * @return Interfaces\RewritesUri
	 */
	private function getUriRewriter()
	{
		return $this->config_delegate->getUriRewriter();
	}

	/**
	 * @return Interfaces\ResolvesUri
	 */
	private function getUriResolver()
	{
		return $this->config_delegate->getUriResolver();
	}

	/**
	 * @return string
	 */
	private function getProjectNamespace()
	{
		return $this->config_delegate->getProjectNamespace();
	}
}