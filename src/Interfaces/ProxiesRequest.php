<?php
namespace IceHawk\IceHawk\Interfaces;

/**
 * Interface ProxiesRequest
 *
 * @package IceHawk\IceHawk\Interfaces
 */
interface ProxiesRequest
{
	public function proxyRequest( ProvidesRequestInfo $request ) : ProvidesRequestInfo;
}