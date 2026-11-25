<?php
/**
 * References in the Bricks data of an imported demo.
 *
 * @package Bricks_Animation_Addons
 */

namespace wealcoder\bricksfly\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * References in the Bricks data of an imported demo.
 *
 * Bricks stores the ids of the demo site: the page a link goes to, the popup or
 * template an element opens, the menu of a nav element, the posts and terms of
 * a query or a template condition, and full URLs of the demo's own pages. This
 * class rewrites all of them to the posts, terms and pages of this site.
 *
 * A reference to something that is not part of the demo's export is removed:
 * the same id belongs to something else here.
 */
class BRICKSFLY_Import_Refs {

	/** Setting keys that hold one post id. */
	const POST_KEYS = array( 'postId', 'templateId', 'switchTemplate', 'no_results_template', 'templatePreviewPostId', 'popupId', 'template' );

	/** Setting keys that hold one menu id. */
	const MENU_KEYS = array( 'menu', 'menuSelected' );

	/** Setting keys that hold a list of post ids. */
	const POST_LIST_KEYS = array( 'ids', 'post__in', 'post__not_in' );

	/**
	 * Demo post id => post id here.
	 *
	 * @var int[]
	 */
	private $posts;

	/**
	 * Demo term id => term id here.
	 *
	 * @var int[]
	 */
	private $terms;

	/**
	 * Menus this import created.
	 *
	 * @var int[]
	 */
	private $menus;

	/**
	 * Path of a demo page => demo post id.
	 *
	 * @var int[]
	 */
	private $links;

	/**
	 * Global class id in the demo => its id here (only the changed ones).
	 *
	 * @var string[]
	 */
	private $classes;

	/**
	 * Address of the demo, without a trailing slash.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Set up.
	 *
	 * @param array $map Option bricksfly_import_id_map (written by the WXR importer).
	 */
	public function __construct( array $map ) {
		$this->posts   = isset( $map['posts'] ) ? (array) $map['posts'] : array();
		$this->terms   = isset( $map['terms'] ) ? (array) $map['terms'] : array();
		$this->menus   = isset( $map['menus'] ) ? array_values( (array) $map['menus'] ) : array();
		$this->links   = isset( $map['links'] ) ? (array) $map['links'] : array();
		$this->classes = isset( $map['classes'] ) ? (array) $map['classes'] : array();

		$this->url = isset( $map['url'] ) ? untrailingslashit( (string) $map['url'] ) : '';
	}

	/**
	 * Post id on this site.
	 *
	 * @param int $id Demo post id.
	 * @return int 0 when the post is not part of the import.
	 */
	public function post( $id ) {
		return isset( $this->posts[ (int) $id ] ) ? (int) $this->posts[ (int) $id ] : 0;
	}

	/**
	 * Rewrite every reference in a value.
	 *
	 * @param mixed      $value Value.
	 * @param string|int $key   Its key.
	 * @return mixed
	 */
	public function apply( $value, $key = '' ) {
		if ( is_array( $value ) ) {
			$out  = array();
			$list = in_array( $key, self::POST_LIST_KEYS, true ) && array_values( $value ) === $value;

			if ( '_cssGlobalClasses' === $key ) {
				foreach ( $value as $k => $class_id ) {
					$value[ $k ] = is_string( $class_id ) && isset( $this->classes[ $class_id ] ) ? $this->classes[ $class_id ] : $class_id;
				}
				return $value;
			}

			foreach ( $value as $k => $item ) {
				if ( $list && is_scalar( $item ) && is_numeric( $item ) ) {
					$id = $this->same_type( $this->post( $item ), $item );
					if ( null !== $id ) {
						$out[] = $id;
					}
					continue;
				}
				$new = $this->apply( $item, $k );
				if ( null === $new && null !== $item ) {
					continue; // A reference to something that is not here.
				}
				if ( $list ) {
					$out[] = $new;
				} else {
					$out[ $k ] = $new;
				}
			}

			return $out;
		}

		if ( is_scalar( $value ) && is_numeric( $value ) && (int) $value > 0 ) {
			if ( in_array( $key, self::POST_KEYS, true ) ) {
				return $this->same_type( $this->post( $value ), $value );
			}
			if ( in_array( $key, self::MENU_KEYS, true ) ) {
				$menu = $this->menus ? (int) $this->menus[0] : 0;
				return $this->same_type( isset( $this->terms[ (int) $value ] ) ? (int) $this->terms[ (int) $value ] : $menu, $value );
			}
			return $value;
		}

		return is_string( $value ) ? $this->text( $value ) : $value;
	}

	/**
	 * The new id as Bricks stored the old one (number or text).
	 *
	 * @param int   $id  New id, 0 when there is none.
	 * @param mixed $old Old value.
	 * @return int|string|null
	 */
	private function same_type( $id, $old ) {
		if ( ! $id ) {
			return null;
		}

		return is_int( $old ) ? $id : (string) $id;
	}

	/**
	 * Rewrite the references in a text: "category::3", [bricks_template id="7"]
	 * and URLs of the demo's own pages. Its files (wp-content) are not touched.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	public function text( $text ) {
		if ( preg_match( '/^([a-z0-9_-]+)::(\d+)$/', $text, $m ) ) {
			return taxonomy_exists( $m[1] ) && isset( $this->terms[ (int) $m[2] ] ) ? $m[1] . '::' . $this->terms[ (int) $m[2] ] : $text;
		}

		if ( false !== strpos( $text, '[bricks_template' ) ) {
			$text = preg_replace_callback(
				'/(\[bricks_template\s+id=["\']?)(\d+)/',
				function ( $m ) {
					$id = $this->post( $m[2] );
					return $id ? $m[1] . $id : $m[0];
				},
				$text
			);
		}

		if ( '' === $this->url || false === strpos( $text, $this->url ) ) {
			return $text;
		}

		return preg_replace_callback(
			'#' . preg_quote( $this->url, '#' ) . '((?:/(?!wp-content/|wp-includes/)[^\s"\'<>)\\\\]*)?)(?=$|[\s"\'<>)\\\\])#',
			function ( $m ) {
				return $this->page_url( $m[1] );
			},
			$text
		);
	}

	/**
	 * Address on this site of a page of the demo.
	 *
	 * @param string $rest What follows the demo's address.
	 * @return string
	 */
	private function page_url( $rest ) {
		$path  = (string) wp_parse_url( 'http://x' . $rest, PHP_URL_PATH );
		$after = substr( $rest, strlen( $path ) );
		$full  = trim( (string) wp_parse_url( $this->url, PHP_URL_PATH ) . $path, '/' );

		if ( isset( $this->links[ $full ] ) && $this->post( $this->links[ $full ] ) ) {
			$url = get_permalink( $this->post( $this->links[ $full ] ) );
			if ( $url ) {
				return $url . $after;
			}
		}

		return untrailingslashit( home_url() ) . $rest;
	}
}
