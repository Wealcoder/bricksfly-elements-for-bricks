<?php
/**
 * Render a Bricks template export JSON through Bricks' own renderer, in memory
 * (nothing is saved to the database). Proves Bricks accepts the elements and
 * generates HTML + CSS for them.
 *
 * Usage: php render-test.php <file.json> [--html]
 */

$_SERVER['HTTP_HOST']      = 'bricks-animation-dev.test';
$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_SCHEME'] = 'http';
ini_set( 'display_errors', '0' );

define( 'WP_USE_THEMES', false );
require dirname( __DIR__, 5 ) . '/wp-load.php';

$file = $argv[1] ?? '';
$data = json_decode( (string) file_get_contents( $file ), true );
if ( ! is_array( $data ) || empty( $data['content'] ) ) {
	fwrite( STDERR, "Cannot read content from $file\n" );
	exit( 1 );
}

\Bricks\Elements::load_elements();

$elements = $data['content'];

// Bricks accumulates generated CSS in Assets::$inline_css instead of returning it.
\Bricks\Assets::generate_css_from_elements( $elements, 'content' );
$css  = \Bricks\Assets::$inline_css['content'] ?? '';
$html = \Bricks\Frontend::render_data( $elements );

$missing = [];
foreach ( $elements as $el ) {
	if ( strpos( $html, 'brxe-' . $el['id'] ) === false ) {
		$missing[] = $el['id'] . ' (' . $el['name'] . ')';
	}
}

echo 'Elements: ' . count( $elements ) . "\n";
echo 'HTML bytes: ' . strlen( $html ) . "\n";
echo 'CSS bytes: ' . strlen( (string) $css ) . "\n";
echo 'Elements not found in HTML: ' . ( $missing ? implode( ', ', $missing ) : 'none' ) . "\n";

if ( in_array( '--html', $argv, true ) ) {
	echo "\n--- CSS ---\n" . $css . "\n--- HTML ---\n" . $html . "\n";
}

exit( $missing ? 1 : 0 );
