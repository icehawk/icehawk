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
use IceHawk\IceHawk\Routing\Interfaces\BypassesRequest;

/**
 * Class RequestBypasser
 * @package IceHawk\IceHawk\Defaults
 */
final class RequestBypasser
{
	/** @var  array|BypassesRequest[] */
	private $requestBypasses = [];

	public function addRequestBypass( BypassesRequest $requestBypass )
	{
		$this->requestBypasses[] = $requestBypass;
	}

	public function bypassRequest( ProvidesRequestInfo $requestInfo ) : ProvidesRequestInfo
	{
		$uri    = $requestInfo->getUri();
		$method = $requestInfo->getMethod();

		foreach ( $this->requestBypasses as $requestBypass )
		{
			if ( $requestBypass->matches( $uri ) && $method != $requestBypass->getFinalMethod() )
			{
				$requestInfo = $this->createNewRequestInfo( $requestInfo, $requestBypass );
			}
		}

		return $requestInfo;
	}

	private function createNewRequestInfo(
		ProvidesRequestInfo $requestInfo,
		BypassesRequest $requestBypass
	) : ProvidesRequestInfo
	{
		$requestMethod = $requestInfo->getMethod();
		$uriParams = $requestBypass->getUriParams();
		$finalMethod = $requestBypass->getFinalMethod();
		$finalUri = $this->buildFinalUri( $requestBypass->getFinalUri(), $uriParams );

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
