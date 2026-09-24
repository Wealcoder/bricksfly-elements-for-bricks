<?php
/**
 * Create / delete a temporary Bricks page from a template export JSON, so the
 * generated design can be screenshotted on the local dev site.
 *
 *   php preview-page.php create <file.json> <slug>   → prints the page URL
 *   php preview-page.php delete <slug>               → permanently deletes it
 */

$_SERVER['HTTP_HOST']      = 'bricks-animation-dev.test';
$_SERVER['REQUEST_URI']    = '/';
$_SERVER['REQUEST_SCHEME'] = 'http';
ini_set( 'display_errors', '0' );

define( 'WP_USE_THEMES', false );
require dirname( __DIR__, 5 ) . '/wp-load.php';

// Bricks only lets users with builder access write its content meta.
$admins = get_users( [ 'role' => 'administrator', 'number' => 1 ] );
if ( $admins ) {
	wp_set_current_user( $admins[0]->ID );
}

$command = $argv[1] ?? '';

if ( $command === 'create' ) {
	$data = json_decode( (string) file_get_contents( $argv[2] ?? '' ), true );
	$slug = sanitize_title( $argv[3] ?? 'zz-figma-poc-preview' );
	if ( empty( $data['content'] ) ) {
		fwrite( STDERR, "No content in file\n" );
		exit( 1 );
	}
	$existing = get_page_by_path( $slug, OBJECT, 'page' );
	if ( $existing ) {
		wp_delete_post( $existing->ID, true );
	}
	$post_id = wp_insert_post(
		[
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => 'ZZ Figma POC preview (' . $slug . ')',
			'post_name'   => $slug,
		]
	);
	update_post_meta( $post_id, BRICKS_DB_EDITOR_MODE, 'bricks' );
	update_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT, $data['content'] );
	echo get_permalink( $post_id ) . "\n";
	exit( 0 );
}

if ( $command === 'delete' ) {
	$page = get_page_by_path( sanitize_title( $argv[2] ?? '' ), OBJECT, 'page' );
	if ( ! $page ) {
		echo "Not found\n";
		exit( 0 );
	}
	wp_delete_post( $page->ID, true );
	echo "Deleted page {$page->ID}\n";
	exit( 0 );
}

fwrite( STDERR, "Usage: create <file.json> <slug> | delete <slug>\n" );
exit( 2 );
