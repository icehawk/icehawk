<?php
namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Constants\HttpMethod;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Routing\Interfaces\RedirectsRoute;

/**
 * Class ProxyRequestInfo
 *
 * @package IceHawk\IceHawk\Defaults
 */
final class ProxyRequestInfo implements ProvidesRequestInfo
{
	/** @var  RequestInfo */
	private $requestInfo;

	/** @var  string */
	private $uri;

	/** @var  string */
	private $method;

	/**
	 * ProxyRequestInfo constructor.
	 *
	 * @param array|\Traversable|RedirectsRoute[] $routeRedirects
	 */
	public function __construct( $routeRedirects )
	{
		$this->requestInfo = RequestInfo::fromEnv();
		$this->uri         = $this->requestInfo->getUri();
		$this->method      = $this->requestInfo->getMethod();

		$this->redirectIfNeeded( $routeRedirects );
	}

	/**
	 * @param array|\Traversable|RedirectsRoute[] $routeRedirects
	 */
	private function redirectIfNeeded( $routeRedirects )
	{
		foreach ( $routeRedirects as $routeRedirect )
		{
			if ( $routeRedirect->matches( $this->uri ) && $this->method != $routeRedirect->getFinalMethod() )
			{
				$this->redirect( $routeRedirect );
			}
		}
	}

	private function redirect( RedirectsRoute $routeRedirect )
	{
		$this->uri = $routeRedirect->getFinalUri();

		$readMethods     = array_intersect(
			[ $this->method, $routeRedirect->getFinalMethod() ], HttpMethod::READ_METHODS
		);
		$readMethodCount = count( $readMethods );

		if ( $readMethodCount == 1 )
		{
			if ( $readMethods[0] == $this->method )
			{
				$_POST = $_GET;
			}
			else
			{
				$_GET = $_POST;
			}
		}
	}

	public function isSecure() : bool
	{
		return $this->requestInfo->isSecure();
	}

	public function getMethod() : string
	{
		return $this->method;
	}

	public function getUri() : string
	{
		return $this->uri;
	}

	public function getHost() : string
	{
		return $this->requestInfo->getHost();
	}

	public function getUserAgent() : string
	{
		return $this->requestInfo->getUserAgent();
	}

	public function getServerAddress() : string
	{
		return $this->requestInfo->getServerAddress();
	}

	public function getClientAddress() : string
	{
		return $this->requestInfo->getClientAddress();
	}

	public function getRequestTimeFloat() : float
	{
		return $this->requestInfo->getRequestTimeFloat();
	}

	public function getAcceptedContentTypes() : string
	{
		return $this->requestInfo->getAcceptedContentTypes();
	}

	public function getQueryString() : string
	{
		return $this->requestInfo->getQueryString();
	}

	public function getReferer() : string
	{
		return $this->requestInfo->getReferer();
	}

	public function getAuthUser() : string
	{
		return $this->requestInfo->getAuthUser();
	}

	public function getAuthPassword() : string
	{
		return $this->requestInfo->getAuthPassword();
	}

	public function getContentType() : string
	{
		return $this->requestInfo->getContentType();
	}

	public function getContentLength() : string
	{
		return $this->requestInfo->getContentLength();
	}
}