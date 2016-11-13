<?php
namespace IceHawk\IceHawk\Defaults;

use IceHawk\IceHawk\Constants\HttpMethod;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\ProxiesRequest;
use IceHawk\IceHawk\Routing\Interfaces\RedirectsRoute;

/**
 * Class RequestProxy
 *
 * @package IceHawk\IceHawk\Defaults
 */
final class RequestProxy implements ProxiesRequest
{
	/** @var  array|\Traversable|RedirectsRoute[] */
	private $routeRedirects;

	/**
	 * ProxyRequestInfo constructor.
	 *
	 * @param array|\Traversable|RedirectsRoute[] $routeRedirects
	 */
	public function __construct( $routeRedirects )
	{
		$this->$routeRedirects = $routeRedirects;
	}

	public function proxyRequest( ProvidesRequestInfo $request ) : ProvidesRequestInfo
	{
		$uri    = $request->getUri();
		$method = $request->getMethod();

		foreach ( $this->routeRedirects as $routeRedirect )
		{
			if ( $routeRedirect->matches( $uri ) && $method != $routeRedirect->getFinalMethod() )
			{
				$request = $this->createNewRequest( $routeRedirect, $method );
			}
		}

		return $request;
	}

	private function createNewRequest( RedirectsRoute $routeRedirect, string $method ) : ProvidesRequestInfo
	{
		$readMethods     = array_intersect( [ $method, $routeRedirect->getFinalMethod() ], HttpMethod::READ_METHODS );
		$readMethodCount = count( $readMethods );

		if ( $readMethodCount == 1 )
		{
			if ( $readMethods[0] == $method )
			{
				$_POST = $_GET;
			}
			else
			{
				$_GET = $_POST;
			}
		}

		$_SERVER['REQUEST_METHOD'] = $routeRedirect->getFinalMethod();
		$_SERVER['REQUEST_URI']    = $routeRedirect->getFinalUri();

		return new RequestInfo( $_SERVER );
	}

}