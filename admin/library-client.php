<?php

namespace wealcoder\bricksfly\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Server-side client for the BricksFly template library.
 *
 * Every import reads the template / page / section it is importing from the
 * library itself, by id, instead of trusting data posted from the browser.
 * All requests stay on the library host (BRICKSFLY_TEMPLATE_STARTER_BASE_URL).
 *
 * The library decides what it delivers: items marked Pro are listed with
 * their `is_pro` flag, but their files are only released to requests the
 * library accepts for Pro content. When it refuses, the import stops with
 * the message below.
 */
class BRICKSFLY_Library_Client {

	/** Cache lifetime for a resolved item. */
	const CACHE_TTL = 10 * MINUTE_IN_SECONDS;

	/**
	 * Library base URL, always with a trailing slash.
	 *
	 * @return string
	 */
	public static function base_url() {
		return trailingslashit( BRICKSFLY_TEMPLATE_STARTER_BASE_URL );
	}

	/**
	 * Full URL of a library REST route.
	 *
	 * @param string $route Route path after `wp-json/`, without a leading slash.
	 * @return string
	 */
	public static function api_url( $route ) {
		return self::base_url() . 'wp-json/' . ltrim( $route, '/' );
	}

	/**
	 * Whether a URL points at the library host.
	 *
	 * @param string $url URL to check.
	 * @return bool
	 */
	public static function is_library_url( $url ) {
		$host   = wp_parse_url( (string) $url, PHP_URL_HOST );
		$scheme = wp_parse_url( (string) $url, PHP_URL_SCHEME );
		$base   = wp_parse_url( self::base_url(), PHP_URL_HOST );

		return is_string( $host ) && is_string( $base )
			&& in_array( $scheme, array( 'http', 'https' ), true )
			&& strtolower( $host ) === strtolower( $base );
	}

	/**
	 * GET a library URL. Refuses any other host.
	 *
	 * @param string $url  Library URL.
	 * @param array  $args Extra request args.
	 * @return array|\WP_Error Response array.
	 */
	public static function get( $url, $args = array() ) {
		if ( ! self::is_library_url( $url ) ) {
			return new \WP_Error( 'bricksfly_library_host', __( 'Template files can only be downloaded from the BricksFly template library.', 'bricksfly-elements-for-bricks' ) );
		}

		return wp_remote_get( $url, wp_parse_args( $args, array( 'timeout' => 60 ) ) );
	}

	/**
	 * Message shown when the library does not release an item.
	 *
	 * @return string
	 */
	public static function pro_item_message() {
		return __( 'This item is part of BricksFly Pro. Install BricksFly Pro to import it.', 'bricksfly-elements-for-bricks' );
	}

	/**
	 * Read one starter template or starter page from the library by id.
	 *
	 * @param string $type 'template' or 'page'.
	 * @param int    $id   Library item id.
	 * @return array|\WP_Error Item as returned by the library.
	 */
	public static function get_template_item( $type, $id ) {
		$id = absint( $id );
		if ( ! $id ) {
			return new \WP_Error( 'bricksfly_library_id', __( 'Template not found.', 'bricksfly-elements-for-bricks' ) );
		}

		$route     = ( 'page' === $type ) ? 'wp/v2/brk-starter-page' : 'wp/v2/brk-templates';
		$cache_key = 'bricksfly_lib_' . ( 'page' === $type ? 'p' : 't' ) . $id;
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) ) {
			return $cached;
		}

		$response = self::get( add_query_arg( array( 'tplid' => $id ), self::api_url( $route ) ) );
		$data     = self::decode( $response );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$item = null;
		if ( ! empty( $data['templates'] ) && is_array( $data['templates'] ) ) {
			foreach ( $data['templates'] as $candidate ) {
				if ( is_array( $candidate ) && isset( $candidate['id'] ) && absint( $candidate['id'] ) === $id ) {
					$item = $candidate;
					break;
				}
			}
		}

		if ( ! $item ) {
			return new \WP_Error( 'bricksfly_library_missing', __( 'Template not found.', 'bricksfly-elements-for-bricks' ) );
		}

		set_transient( $cache_key, $item, self::CACHE_TTL );

		return $item;
	}

	/**
	 * Ask the library for the content file of a starter template / page.
	 *
	 * @param int $id Library item id.
	 * @return array|\WP_Error { type, content_url, id }
	 */
	public static function get_content_file( $id ) {
		$url      = add_query_arg( array( 'template' => array( 'id' => absint( $id ) ) ), self::api_url( 'brk-starter-templates/download' ) );
		$response = self::get( $url, array( 'timeout' => 90 ) );

		if ( ! is_wp_error( $response ) && 403 === (int) wp_remote_retrieve_response_code( $response ) ) {
			return new \WP_Error( 'bricksfly_pro_item', self::pro_item_message() );
		}

		$file = self::decode( $response );

		if ( is_wp_error( $file ) ) {
			return $file;
		}

		if ( empty( $file['content_url'] ) || ! self::is_library_url( $file['content_url'] ) ) {
			return new \WP_Error( 'bricksfly_library_file', __( 'The template file is missing. Please contact the template author.', 'bricksfly-elements-for-bricks' ) );
		}

		return array(
			'type'        => isset( $file['type'] ) ? sanitize_key( $file['type'] ) : 'xml',
			'content_url' => esc_url_raw( $file['content_url'] ),
			'id'          => isset( $file['id'] ) ? absint( $file['id'] ) : 0,
		);
	}

	/**
	 * Read one section from the library by id.
	 *
	 * @param int $id Library section id.
	 * @return array|\WP_Error Section as returned by the library.
	 */
	public static function get_section( $id ) {
		$response = self::get( self::api_url( 'bricks-sections/v1/list/' . absint( $id ) ), array( 'timeout' => 30 ) );

		return self::decode( $response );
	}

	/**
	 * Decode a JSON library response.
	 *
	 * @param array|\WP_Error $response HTTP response.
	 * @return array|\WP_Error
	 */
	private static function decode( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$data = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $code || ! is_array( $data ) ) {
			return new \WP_Error( 'bricksfly_library_response', __( 'The template library could not be reached. Please try again.', 'bricksfly-elements-for-bricks' ) );
		}

		return $data;
	}
}
