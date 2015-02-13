<?php
/**
 * @author hollodotme
 */

namespace Fortuneglobe\IceHawk\Interfaces;

interface ServesAppConfiguration
{
	public function configureSession();

	public function configureErrorHandling();

	/**
	 * @return string
	 */
	public function getProjectNamespace();

	/**
	 * @return RewritesUri
	 */
	public function getUriRewriter();

	/**
	 * @return ResolvesUri
	 */
	public function getUriResolver();

	public function getSessionRegistry();
}