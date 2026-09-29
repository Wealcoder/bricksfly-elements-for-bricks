<?php

namespace wealcoder\bricksfly\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Brings a library section's images into the site's Media Library.
 *
 * A section from the template library is a Bricks export: its image / SVG
 * settings carry the attachment id and URLs of the site it was built on.
 * Pasted as-is, those ids point at unrelated (or missing) attachments on this
 * site, and the files stay on the remote host. Before the section is handed
 * to the builder, each image and SVG from the BricksFly media hosts is
 * downloaded once into the Media Library and the settings are rewritten to the
 * local attachment. Files already imported earlier are reused.
 *
 * Nothing is saved to the page here: the rewritten section still goes through
 * Bricks' own paste, and is only stored when the user saves.
 */
class BRICKSFLY_Section_Media {

	/**
	 * Upper bound of files downloaded for one section insert.
	 */
	const MAX_FILES = 40;

	/**
	 * Image types downloaded into the Media Library.
	 */
	const IMAGE_EXTENSIONS = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'avif' );

	/**
	 * Original URL => local attachment id, for this request.
	 *
	 * @var array<string,int>
	 */
	private $imported = array();

	/**
	 * Original URL => local URL, applied to every string in the section.
	 *
	 * @var array<string,string>
	 */
	private $url_map = array();

	/**
	 * Files downloaded in this request.
	 *
	 * @var int
	 */
	private $downloads = 0;

	/**
	 * SVGs left on the remote host because this user may not upload SVG files.
	 *
	 * @var int
	 */
	public $skipped_svgs = 0;

	/**
	 * Hosts section media may be downloaded from: the template library and
	 * the BricksFly demo hosts its sections are built on.
	 *
	 * @return string[]
	 */
	public static function allowed_hosts() {
		$hosts = array(
			(string) wp_parse_url( BRICKSFLY_Library_Client::base_url(), PHP_URL_HOST ),
			'themecrowdy.com',
			'crowdytheme.com',
			'bricksfly.com',
			'wealcoder.com',
		);

		/**
		 * Filter the hosts section images may be downloaded from. Subdomains of
		 * a listed host are allowed too.
		 *
		 * @param string[] $hosts Host names.
		 */
		return array_filter( array_map( 'strtolower', (array) apply_filters( 'bricksfly_section_media_hosts', $hosts ) ) );
	}

	/**
	 * Whether a URL is on an allowed media host.
	 *
	 * @param string $url URL.
	 * @return bool
	 */
	public static function is_allowed_url( $url ) {
		$scheme = wp_parse_url( $url, PHP_URL_SCHEME );
		$host   = wp_parse_url( $url, PHP_URL_HOST );

		if ( ! in_array( $scheme, array( 'http', 'https' ), true ) || ! is_string( $host ) ) {
			return false;
		}

		$host = strtolower( $host );

		foreach ( self::allowed_hosts() as $allowed ) {
			if ( $host === $allowed || substr( $host, -strlen( '.' . $allowed ) ) === '.' . $allowed ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Localize the media of a resolved section.
	 *
	 * @param array $elements       Section elements.
	 * @param array $global_classes Global classes the section carries.
	 * @return array{0:array,1:array} Rewritten elements and global classes.
	 */
	public function localize( array $elements, array $global_classes ) {
		$elements       = $this->walk( $elements );
		$global_classes = $this->walk( $global_classes );

		// URLs of downloaded files used anywhere else (custom CSS, HTML, …).
		if ( $this->url_map ) {
			$elements       = $this->replace_urls( $elements );
			$global_classes = $this->replace_urls( $global_classes );
		}

		return array( $elements, $global_classes );
	}

	/**
	 * Walk settings, localizing every image / SVG object.
	 *
	 * @param mixed $value Settings value.
	 * @return mixed
	 */
	private function walk( $value ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		if ( $this->is_media_object( $value ) ) {
			return $this->localize_object( $value );
		}

		foreach ( $value as $key => $item ) {
			$value[ $key ] = $this->walk( $item );
		}

		return $value;
	}

	/**
	 * An image / SVG setting: { id?, url, full?, size?, filename? } with an image URL.
	 *
	 * @param array $value Settings array.
	 * @return bool
	 */
	private function is_media_object( array $value ) {
		if ( empty( $value['url'] ) || ! is_string( $value['url'] ) ) {
			return false;
		}

		if ( ! isset( $value['id'] ) && ! isset( $value['full'] ) && ! isset( $value['filename'] ) ) {
			return false;
		}

		$ext = self::extension( $value['url'] );

		return 'svg' === $ext || in_array( $ext, self::IMAGE_EXTENSIONS, true );
	}

	/**
	 * Rewrite one image / SVG setting to a local attachment.
	 *
	 * @param array $image Image setting.
	 * @return array
	 */
	private function localize_object( array $image ) {
		$is_svg = 'svg' === self::extension( $image['url'] );
		$source = ( ! $is_svg && ! empty( $image['full'] ) && is_string( $image['full'] ) ) ? $image['full'] : $image['url'];

		$attachment_id = self::is_allowed_url( $source ) ? $this->import_file( $source, $is_svg ) : 0;

		if ( ! $attachment_id ) {
			// Keep the remote URL, but never an id from another site: it would
			// resolve to an unrelated attachment here.
			unset( $image['id'] );
			return $image;
		}

		$size = ( ! $is_svg && ! empty( $image['size'] ) && is_string( $image['size'] ) ) ? $image['size'] : 'full';
		$full = wp_get_attachment_url( $attachment_id );
		$url  = $is_svg ? $full : wp_get_attachment_image_url( $attachment_id, $size );

		$image['id']       = $attachment_id;
		$image['filename'] = wp_basename( (string) $full );
		$image['url']      = $url ? $url : $full;

		if ( ! $is_svg ) {
			$image['full'] = wp_get_attachment_image_url( $attachment_id, 'full' );
			$image['size'] = $size;
		}

		if ( ! empty( $image['full'] ) && $source !== $image['full'] ) {
			$this->url_map[ $source ] = $image['full'];
		}

		return $image;
	}

	/**
	 * Local attachment for a remote file: reuse an earlier import, else download.
	 *
	 * @param string $url    Remote file URL (allowed host).
	 * @param bool   $is_svg Whether the file is an SVG.
	 * @return int Attachment id, 0 on failure.
	 */
	private function import_file( $url, $is_svg ) {
		if ( isset( $this->imported[ $url ] ) ) {
			return $this->imported[ $url ];
		}

		$existing = $this->find_existing( $url );
		if ( $existing ) {
			$this->imported[ $url ] = $existing;
			return $existing;
		}

		if ( ! current_user_can( 'upload_files' ) || $this->downloads >= self::MAX_FILES ) {
			return 0;
		}

		if ( $is_svg && ! $this->can_upload_svg() ) {
			++$this->skipped_svgs;
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		++$this->downloads;

		$tmp = download_url( $url, 30 );
		if ( is_wp_error( $tmp ) ) {
			return 0;
		}

		$file = array(
			'name'     => sanitize_file_name( wp_basename( (string) wp_parse_url( $url, PHP_URL_PATH ) ) ),
			'tmp_name' => $tmp,
			'type'     => $is_svg ? 'image/svg+xml' : '',
			'size'     => (int) filesize( $tmp ),
			'error'    => 0,
		);

		// SVGs go through the same sanitizer as a normal upload (Bricks
		// registers it on this filter; it cleans the file in place).
		if ( $is_svg ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core upload filter, applied so the site's SVG sanitizer runs on the downloaded file.
			$file = apply_filters( 'wp_handle_upload_prefilter', $file );

			if ( ! empty( $file['error'] ) ) {
				wp_delete_file( $tmp );
				return 0;
			}
		}

		$attachment_id = media_handle_sideload( $file, 0 );

		if ( is_wp_error( $attachment_id ) ) {
			wp_delete_file( $tmp );
			return 0;
		}

		// Same marker Bricks uses for imported template images, so either
		// importer reuses the other's download.
		update_post_meta( $attachment_id, '_bricks_image_origin_url', $url );

		$this->imported[ $url ] = (int) $attachment_id;

		return (int) $attachment_id;
	}

	/**
	 * Attachment previously imported from this URL.
	 *
	 * @param string $url Remote file URL.
	 * @return int Attachment id or 0.
	 */
	private function find_existing( $url ) {
		$ids = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key, WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- Single lookup per image on insert.
				'meta_key'       => '_bricks_image_origin_url',
				'meta_value'     => $url, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		return $ids ? (int) $ids[0] : 0;
	}

	/**
	 * Whether this user may upload SVG files (Bricks > Settings > SVG uploads).
	 *
	 * @return bool
	 */
	private function can_upload_svg() {
		if ( class_exists( '\Bricks\Capabilities' ) && method_exists( '\Bricks\Capabilities', 'current_user_can_upload_svg' ) ) {
			return (bool) \Bricks\Capabilities::current_user_can_upload_svg();
		}

		return false;
	}

	/**
	 * Replace downloaded file URLs inside every string of a settings tree.
	 *
	 * @param mixed $value Settings value.
	 * @return mixed
	 */
	private function replace_urls( $value ) {
		if ( is_string( $value ) ) {
			return strtr( $value, $this->url_map );
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				$value[ $key ] = $this->replace_urls( $item );
			}
		}

		return $value;
	}

	/**
	 * Lower-case file extension of a URL.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	private static function extension( $url ) {
		$path = (string) wp_parse_url( $url, PHP_URL_PATH );

		return strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
	}
}
