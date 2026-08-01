<?php
/**
 * Build a distribution zip for this plugin from .distignore rules.
 *
 * Usage: php build-zip.php [target.zip]
 *
 * @package Read_Only_For_GravityForms
 */

$root = __DIR__;
$slug = basename( $root );

// Read the plugin header for the version, used in the default zip name.
$header = file_get_contents( $root . '/readonly-for-gravityforms.php' );
preg_match( '/^\s*\*\s*Version:\s*([0-9.]+)/m', $header, $matches );
$version = isset( $matches[1] ) ? $matches[1] : 'dev';

$target = isset( $argv[1] ) ? $argv[1] : $root . '/' . $slug . '-' . $version . '.zip';
if ( file_exists( $target ) ) {
	unlink( $target );
}

$patterns = array();
$lines    = file( $root . '/.distignore', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
foreach ( $lines as $line ) {
	$line = trim( $line );
	if ( '' !== $line && '#' !== $line[0] ) {
		$patterns[] = ltrim( $line, '/' );
	}
}

$zip = new ZipArchive();
$zip->open( $target, ZipArchive::CREATE | ZipArchive::OVERWRITE );

$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
);

foreach ( $iterator as $file ) {
	$rel = substr( $file->getPathname(), strlen( $root ) + 1 );
	if ( is_excluded( $rel, $patterns ) ) {
		continue;
	}
	$zip->addFile( $file->getPathname(), $slug . '/' . $rel );
}

$zip->close();
echo 'Built ' . $target . PHP_EOL;

/**
 * Whether a path is excluded by any distignore pattern.
 *
 * @param string $rel      Path relative to the plugin root.
 * @param array  $patterns Distignore patterns.
 *
 * @return bool
 */
function is_excluded( $rel, $patterns ) {
	foreach ( $patterns as $pattern ) {
		if ( $rel === $pattern || 0 === strpos( $rel, $pattern . '/' ) || fnmatch( $pattern, basename( $rel ) ) ) {
			return true;
		}
	}
	return false;
}
