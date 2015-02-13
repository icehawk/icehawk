<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk;

use Fortuneglobe\IceHawk\Builders\RequestBuilder;
use Fortuneglobe\IceHawk\Interfaces\ServesAppConfiguration;
use Fortuneglobe\IceHawk\Interfaces\ServesRequestInfo;

final class IceHawk
{

	/** @var ServesAppConfiguration */
	private $configDelegate;

	/** @var SessionRegistry */
	private $sessionRegistry;

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
	 * @param ServesAppConfiguration $configDelegate
	 */
	public function init( ServesAppConfiguration $configDelegate = null )
	{
		if ( is_null( $configDelegate ) )
		{
			$this->configDelegate = new IceHawkDelegate();
		}
		else
		{
			$this->configDelegate = $configDelegate;
		}

		$this->configure();
	}

	private function configure()
	{
		$this->configureErrorHandling();
		$this->configureSession();
	}

	private function configureErrorHandling()
	{
		$this->configDelegate->configureErrorHandling();
	}

	private function configureSession()
	{
		$this->configDelegate->configureSession();
	}

	/**
	 * @return SessionRegistry
	 */
	public function getSessionRegistry()
	{
		$this->initSessionRegistryIfNeeded();

		return $this->sessionRegistry;
	}

	private function initSessionRegistryIfNeeded()
	{
		if ( is_null( $this->sessionRegistry ) )
		{
			session_start();
			$this->sessionRegistry = $this->configDelegate->getSessionRegistry();
		}
	}

	/**
	 * @return RequestHandler
	 */
	public function getRequestHandler()
	{
		$requestInfo = RequestInfo::fromEnv();
		$request     = $this->getRequest( $requestInfo );

		$requestHandlerDelegate = new RequestHandlerDelegate(
			$this->getUriRewriter(),
			$this->getUriResolver(),
			$this->getProjectNamespace()
		);

		return new RequestHandler( $requestInfo, $requestHandlerDelegate, $request );
	}

	/**
	 * @return Interfaces\RewritesUri
	 */
	private function getUriRewriter()
	{
		return $this->configDelegate->getUriRewriter();
	}

	/**
	 * @return Interfaces\ResolvesUri
	 */
	private function getUriResolver()
	{
		return $this->configDelegate->getUriResolver();
	}

	/**
	 * @return string
	 */
	private function getProjectNamespace()
	{
		return $this->configDelegate->getProjectNamespace();
	}

	/**
	 * @param ServesRequestInfo $requestInfo
	 *
	 * @throws Exceptions\InvalidRequestMethod
	 * @return Interfaces\ServesGetRequestData|Interfaces\ServesPostRequestData
	 */
	private function getRequest( ServesRequestInfo $requestInfo )
	{
		$requestBuilder = new RequestBuilder( $requestInfo );

		return $requestBuilder->buildRequest( $_GET, $_POST, $_FILES );
	}
}