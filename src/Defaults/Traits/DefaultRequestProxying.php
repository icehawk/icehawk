<?php
namespace IceHawk\IceHawk\Defaults\Traits;

use IceHawk\IceHawk\Interfaces\ProvidesRequestInfo;
use IceHawk\IceHawk\Interfaces\ProxiesRequest;

trait DefaultRequestProxying
{
	public function getRequestProxy() : ProxiesRequest
	{
		return new class implements ProxiesRequest
		{
			public function proxyRequest( ProvidesRequestInfo $request ) : ProvidesRequestInfo
			{
				return $request;
			}
		};
	}
}