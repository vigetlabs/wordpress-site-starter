#!/usr/bin/env php
<?php
/**
 * Check that every Composer event callable exists in the handlers that receive it.
 *
 * Composer prints a notice and carries on when a script callable is missing, so a
 * handler that never runs looks exactly like one that does nothing.
 *
 * The theme's composer.json travels into generated projects, where
 * ProjectEventHandler.dist.php becomes the handler - its callables have to exist in
 * both files. The root composer.json only ever runs against the dev handler.
 */

$root = dirname( __DIR__ );

$checks = [
	'wp-content/themes/wp-starter/composer.json' => [
		'bin/composer-scripts/ProjectEventHandler.php',
		'bin/composer-scripts/ProjectEventHandler.dist.php',
	],
	'composer.json' => [
		'bin/composer-scripts/ProjectEventHandler.php',
	],
];

$errors = [];

foreach ( $checks as $manifest => $handlers ) {
	$manifestPath = $root . '/' . $manifest;

	if ( ! file_exists( $manifestPath ) ) {
		continue;
	}

	$scripts = json_decode( file_get_contents( $manifestPath ), true )['scripts'] ?? [];
	$methods = [];

	array_walk_recursive(
		$scripts,
		function ( $callable ) use ( &$methods ): void {
			if ( is_string( $callable ) && preg_match( '/ProjectEventHandler::(\w+)$/', $callable, $match ) ) {
				$methods[] = $match[1];
			}
		}
	);

	foreach ( $handlers as $handler ) {
		$handlerPath = $root . '/' . $handler;

		if ( ! file_exists( $handlerPath ) ) {
			continue;
		}

		$contents = file_get_contents( $handlerPath );

		foreach ( array_unique( $methods ) as $method ) {
			if ( ! preg_match( '/function\s+' . preg_quote( $method, '/' ) . '\s*\(/', $contents ) ) {
				$errors[] = sprintf( '%s calls %s(), missing from %s', $manifest, $method, $handler );
			}
		}
	}
}

if ( $errors ) {
	echo implode( PHP_EOL, $errors ) . PHP_EOL;
	exit( 1 );
}

echo 'Composer event handlers are in sync.' . PHP_EOL;
