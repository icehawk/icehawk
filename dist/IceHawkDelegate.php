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
	 * @return Twig
	 */
	public function getTemplateEngine()
	{
		return new Twig( [ __DIR__ ], '/tmp/twig/cache' );
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

	public function configureSession()
	{
		ini_set( 'session.name', 'ihsid' );
		ini_set( 'session.save_handler', 'memcached' );
		ini_set( 'session.save_path', '127.0.0.1:11211' );
		ini_set( 'session.cookie_httponly', true );
		ini_set( 'session.cookie_lifetime', 60 * 60 * 24 );
	}

	public function configureErrorHandling()
	{
		error_reporting( E_ALL );
		ini_set( 'display_errors', 1 );
	}

	/**
	 * @return SessionRegistry
	 */
	public function getSessionRegistry()
	{
		return new SessionRegistry( $_SESSION );
	}
}