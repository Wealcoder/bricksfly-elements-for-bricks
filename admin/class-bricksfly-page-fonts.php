<?php
/**
 * Custom fonts of a page that is imported on its own.
 *
 * The file of a starter page has the page only; the custom fonts it uses are in
 * the file of the starter template it belongs to. They are added to the page's
 * file before it is imported, so they come in (or an earlier import is used
 * again) like the fonts of a full demo.
 *
 * @package BricksFly
 */

namespace wealcoder\bricksfly\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the fonts of the demo to a page's content file.
 */
class BRICKSFLY_Page_Fonts {

	/**
	 * Font ids a text refers to.
	 *
	 * @param string $text Any text (a content file, JSON).
	 * @return int[]
	 */
	public static function refs( $text ) {
		return preg_match_all( '/\bcustom_font_(\d+)\b/', (string) $text, $m ) ? array_values( array_unique( array_map( 'intval', $m[1] ) ) ) : array();
	}

	/**
	 * Add the fonts a page needs to its content file.
	 *
	 * @param string $file      Downloaded content file of the page.
	 * @param string $demo_link Demo address of the page.
	 * @param int[]  $more      Font ids its classes and styles refer to.
	 * @return bool False when the library did not answer.
	 */
	public static function add_to_file( $file, $demo_link, array $more ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- A local file.
		$content = is_string( $file ) && is_readable( $file ) ? (string) file_get_contents( $file ) : '';
		$at      = strrpos( $content, '</channel>' );
		$ids     = array_values( array_unique( array_merge( self::refs( $content ), array_map( 'intval', $more ) ) ) );

		if ( false === $at || ! $ids ) {
			return true;
		}

		$template = BRICKSFLY_Library_Client::find_template_by_demo( $demo_link );
		$items    = $template ? self::items( $template, $ids ) : null;

		if ( null === $items ) {
			return false;
		}
		if ( '' !== $items ) {
			return ! is_wp_error( Base\Helpers::write_to_file( substr( $content, 0, $at ) . $items . "\n" . substr( $content, $at ), $file ) );
		}

		return true;
	}

	/**
	 * The font items of a starter template, with their files.
	 *
	 * @param int   $template Library id of the template.
	 * @param int[] $ids      Font ids.
	 * @return string|null XML, '' when there are none, null when the library did not answer.
	 */
	private static function items( $template, array $ids ) {
		sort( $ids );
		$key    = 'bricksfly_pf_' . md5( $template . ':' . implode( ',', $ids ) );
		$cached = get_transient( $key );
		if ( is_string( $cached ) ) {
			return $cached;
		}

		$release = BRICKSFLY_Library_Client::get_content_file( $template );
		if ( is_wp_error( $release ) ) {
			return null;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		$file = download_url( $release['content_url'], 60 );
		if ( is_wp_error( $file ) ) {
			return null;
		}

		$fonts = self::read( $file, 'bricks_fonts', $ids );
		$media = array();
		foreach ( $fonts as $xml ) {
			if ( preg_match( '#<wp:meta_key><!\[CDATA\[bricks_font_faces\]\]></wp:meta_key>\s*<wp:meta_value><!\[CDATA\[(.*?)\]\]></wp:meta_value>#s', $xml, $m ) && preg_match_all( '/(?<=[;{])(?:s:\d+:"(?!unicode-range|font-weight-range)[a-z0-9]+";)(?:i:(\d+)|s:\d+:"(\d+)");/', $m[1], $n ) ) {
				$media = array_merge( $media, array_filter( array_map( 'intval', array_merge( $n[1], $n[2] ) ) ) );
			}
		}
		$items = $fonts ? implode( "\n", array_merge( $media ? self::read( $file, 'attachment', $media ) : array(), $fonts ) ) : '';
		wp_delete_file( $file );

		set_transient( $key, $items, DAY_IN_SECONDS );

		return $items;
	}

	/**
	 * Items of one post type from a content file.
	 *
	 * @param string $file Path.
	 * @param string $type Post type.
	 * @param int[]  $ids  Post ids of the demo.
	 * @return string[] XML of each item.
	 */
	private static function read( $file, $type, array $ids ) {
		$out    = array();
		$reader = new \XMLReader();
		if ( ! $reader->open( $file, null, LIBXML_NONET | LIBXML_PARSEHUGE ) ) {
			return $out;
		}

		$more = $reader->read();
		while ( $more ) {
			if ( \XMLReader::ELEMENT !== $reader->nodeType || 'item' !== $reader->name ) {
				$more = $reader->read();
				continue;
			}
			$xml = $reader->readOuterXml();
			if ( false !== strpos( $xml, '<wp:post_type><![CDATA[' . $type . ']]>' ) && preg_match( '#<wp:post_id>(\d+)</wp:post_id>#', $xml, $m ) && in_array( (int) $m[1], $ids, true ) ) {
				$out[] = $xml;
			}
			$more = $reader->next();
		}
		$reader->close();

		return $out;
	}
}
