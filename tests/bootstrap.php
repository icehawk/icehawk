<?php declare(strict_types=1);

if (
	extension_loaded( 'xdebug' )
	&& version_compare( '2.6.0', phpversion( 'xdebug' ), '<=' )
)
{
	/** @noinspection PhpUndefinedFunctionInspection */
	/** @noinspection PhpUndefinedConstantInspection */
	xdebug_set_filter(
		XDEBUG_FILTER_CODE_COVERAGE,
		XDEBUG_PATH_WHITELIST,
		[dirname( __DIR__ ) . '/src']
	);
}

require_once __DIR__ . '/../vendor/autoload.php';
