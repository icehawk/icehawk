<?php declare(strict_types = 1);
/**
 * Copyright (c) 2016 Holger Woltersdorf & Contributors
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

namespace IceHawk\IceHawk\Routing;

use IceHawk\IceHawk\Constants\HttpMethod;
use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Routing\Interfaces\RedirectsRoute;

/**
 * Class RequestProxy
 * @package IceHawk\IceHawk\Defaults
 */
final class RequestProxy
{
	/** @var  array|RedirectsRoute[] */
	private $routeRedirects = [];

	public function addRedirect( RedirectsRoute $redirect )
	{
		$this->routeRedirects[] = $redirect;
	}

	public function proxyRequest( ProvidesRequestInfo $requestInfo ) : ProvidesRequestInfo
	{
		$uri    = $requestInfo->getUri();
		$method = $requestInfo->getMethod();

		foreach ( $this->routeRedirects as $redirect )
		{
			if ( $redirect->matches( $uri ) && $method != $redirect->getFinalMethod() )
			{
				$requestInfo = $this->createNewRequest( $requestInfo, $redirect );
			}
		}

		return $requestInfo;
	}

	private function createNewRequest(
		ProvidesRequestInfo $requestInfo,
		RedirectsRoute $redirect
	) : ProvidesRequestInfo
	{
		$requestMethod = $requestInfo->getMethod();
		$uriParams  = $redirect->getUriParams();
		$finalMethod   = $redirect->getFinalMethod();
		$finalUri      = $this->buildFinalUri( $redirect->getFinalUri(), $uriParams );

		$overWrites = [ 'REQUEST_METHOD' => $finalMethod, 'REQUEST_URI' => $finalUri ];

		if ( $this->isWriteMethod( $requestMethod ) && $this->isReadMethod( $finalMethod ) )
		{
			$_GET = $_POST;

			$overWrites['QUERY_STRING'] = $this->createQueryString( $requestInfo, $uriParams );
		}
		elseif ( $this->isReadMethod( $requestMethod ) && $this->isWriteMethod( $finalMethod ) )
		{
			$_POST = array_merge( $_GET, $uriParams );
		}
		elseif ( $this->isWriteMethod( $requestMethod ) && $this->isWriteMethod( $finalMethod ) )
		{
			$_POST = array_merge( $_POST, $uriParams );
		}
		else
		{
			$overWrites['QUERY_STRING'] = $this->createQueryString( $requestInfo, $uriParams );
		}

		return $requestInfo->newWithOverwrites( $overWrites );
	}

	private function buildFinalUri( string $finalUri, array $uriParams ) : string
	{
		array_walk(
			$uriParams,
			function ( $val, $key ) use ( &$finalUri )
			{
				$finalUri = str_replace( ':' . $key, $val, $finalUri );
			}
		);

		return $finalUri;
	}

	private function isReadMethod( string $httpMethod ) : bool
	{
		return in_array( $httpMethod, HttpMethod::READ_METHODS );
	}

	private function isWriteMethod( string $httpMethod ) : bool
	{
		return in_array( $httpMethod, HttpMethod::WRITE_METHODS );
	}

	private function createQueryString( ProvidesRequestInfo $requestInfo, array $uriParams ) : string
	{
		parse_str( $requestInfo->getQueryString(), $queryData );

		$queryData = array_merge( $queryData, $uriParams );

		return http_build_query( $queryData );
	}
}
