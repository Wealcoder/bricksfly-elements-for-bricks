<?php

namespace wealcoder\bricksfly\Admin\Base;

use WP_Error;
use XMLReader;

defined( 'ABSPATH' ) || die();

class WXRImporter extends \WP_Importer {

	const MAX_WXR_VERSION = 1.2;

	const REGEX_HAS_ATTACHMENT_REFS = '!
		(
			# Match anything with an image or attachment class
			class=[\'"].*?\b(wp-image-\d+|attachment-[\w\-]+)\b
		|
			# Match anything that looks like an upload URL
			src=[\'"][^\'"]*(
				[0-9]{4}/[0-9]{2}/[^\'"]+\.(jpg|jpeg|png|gif)
			|
				content/uploads[^\'"]+
			)[\'"]
		)!ix';

	protected $version = '1.0';

	protected $categories = array();
	protected $tags       = array();
	protected $base_url   = '';

	protected $processed_terms      = array();
	protected $processed_posts      = array();
	protected $processed_menu_items = array();
	protected $menu_item_orphans    = array();
	protected $missing_menu_items   = array();

	public    $options            = array();
	protected $mapping            = array();
	protected $requires_remapping = array();
	protected $exists             = array();
	protected $user_slug_override = array();

	protected $url_remap       = array();
	protected $featured_images = array();
	protected $background_attachments = array();

	protected $logger;

	public function __construct( $options = array() ) {
		$empty_types = array(
			'post'    => array(),
			'comment' => array(),
			'term'    => array(),
			'user'    => array(),
		);

		$this->mapping                = $empty_types;
		$this->mapping['user_slug']   = array();
		$this->mapping['term_id']     = array();
		$this->requires_remapping     = $empty_types;
		$this->exists                 = $empty_types;

		$this->options = wp_parse_args( $options, array(
			'prefill_existing_posts'    => true,
			'prefill_existing_comments' => true,
			'prefill_existing_terms'    => true,
			'update_attachment_guids'   => false,
			'fetch_attachments'         => true,
			'aggressive_url_search'     => false,
			'default_author'            => null,
		) );
	}

	public function set_logger( $logger ) {
		$this->logger = $logger;
	}

	protected function get_reader( $file ) {
		$reader = new XMLReader();
		$status = $reader->open( $file );

		if ( ! $status ) {
			return new WP_Error( 'bricksfly_importer.cannot_parse', __( 'Could not open the file for parsing', 'bricksfly-elements-for-bricks' ) );
		}

		return $reader;
	}

	public function get_preliminary_information( $file ) {
		$reader = $this->get_reader( $file );
		if ( is_wp_error( $reader ) ) {
			return $reader;
		}

		$this->version = '1.0';

		$data = new WXRImportInfo();
		while ( $reader->read() ) {
			if ( $reader->nodeType !== XMLReader::ELEMENT ) {
				continue;
			}

			switch ( $reader->name ) {
				case 'wp:wxr_version':
					$this->version = $reader->readString();
					$reader->next();
					break;

				case 'generator':
					$data->generator = $reader->readString();
					$reader->next();
					break;

				case 'title':
					$data->title = $reader->readString();
					$reader->next();
					break;

				case 'wp:base_site_url':
					$data->siteurl = $reader->readString();
					$reader->next();
					break;

				case 'wp:base_blog_url':
					$data->home = $reader->readString();
					$reader->next();
					break;

				case 'wp:author':
					$node   = $reader->expand();
					$parsed = $this->parse_author_node( $node );
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					$data->users[] = $parsed;
					$reader->next();
					break;

				case 'item':
					$node   = $reader->expand();
					$parsed = $this->parse_post_node( $node );
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					if ( $parsed['data']['post_type'] === 'attachment' ) {
						$data->media_count++;
					} else {
						$data->post_count++;
					}
					$data->comment_count += count( $parsed['comments'] );
					$reader->next();
					break;

				case 'wp:category':
				case 'wp:tag':
				case 'wp:term':
					$data->term_count++;
					$reader->next();
					break;
			}
		}

		$data->version = $this->version;

		return $data;
	}

	public function parse_authors( $file ) {
		$reader = $this->get_reader( $file );
		if ( is_wp_error( $reader ) ) {
			return $reader;
		}

		$this->version = '1.0';

		$authors = array();
		while ( $reader->read() ) {
			if ( $reader->nodeType !== XMLReader::ELEMENT ) {
				continue;
			}

			switch ( $reader->name ) {
				case 'wp:wxr_version':
					$this->version = $reader->readString();
					if ( version_compare( $this->version, self::MAX_WXR_VERSION, '>' ) ) {
						$this->logger->warning( sprintf(
							/* translators: 1: WXR file version detected in the import file, 2: maximum WXR version this importer supports. */
							__( 'This WXR file (version %1$s) is newer than the importer (version %2$s) and may not be supported. Please consider updating.', 'bricksfly-elements-for-bricks' ),
							$this->version,
							self::MAX_WXR_VERSION
						) );
					}
					$reader->next();
					break;

				case 'wp:author':
					$node   = $reader->expand();
					$parsed = $this->parse_author_node( $node );
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					$authors[] = $parsed;
					$reader->next();
					break;
			}
		}

		return $authors;
	}

	public function import( $file ) {
		add_filter('bricksfly_import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
		add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

		$result = $this->import_start( $file );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		$data       = (array) $this->get_preliminary_information( $file );
		$total_init = 0;
		$temp_title = '';
		if ( ! is_wp_error( $data ) ) {
			$total_init = $data['post_count'] + $data['media_count'] + $data['comment_count'] + $data['term_count'];
			$temp_title = isset( $data['title'] ) ? $data['title'] : '';
		}

		$reader = $this->get_reader( $file );
		if ( is_wp_error( $reader ) ) {
			return $reader;
		}

		$this->version  = '1.0';
		$this->base_url = '';

		$bricksfly_counter_progress = 0;

		while ( $reader->read() ) {
			if ( $reader->nodeType !== XMLReader::ELEMENT ) {
				continue;
			}

			switch ( $reader->name ) {
				case 'wp:wxr_version':
					$this->version = $reader->readString();
					if ( version_compare( $this->version, self::MAX_WXR_VERSION, '>' ) ) {
						$this->logger->warning( sprintf(
							/* translators: 1: WXR file version detected in the import file, 2: maximum WXR version this importer supports. */
							__( 'This WXR file (version %1$s) is newer than the importer (version %2$s) and may not be supported. Please consider updating.', 'bricksfly-elements-for-bricks' ),
							$this->version,
							self::MAX_WXR_VERSION
						) );
					}
					$reader->next();
					break;

				case 'wp:base_site_url':
					$this->base_url = $reader->readString();
					$reader->next();
					break;

				case 'item':
					$node   = $reader->expand();
					$parsed = $this->parse_post_node( $node );
					$bricksfly_counter_progress += 1;
					update_option( 'bricksfly_template_import_progress', [
						'type'        => 'single',
						'total_items' => $total_init,
						'title'       => $temp_title,
						'progress'    => $bricksfly_counter_progress,
						'data'        => [ 'Posts: ' . $bricksfly_counter_progress ],
					] );
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					$this->process_post( $parsed['data'], $parsed['meta'], $parsed['comments'], $parsed['terms'] );
					$reader->next();
					break;

				case 'wp:author':
					$node   = $reader->expand();
					$parsed = $this->parse_author_node( $node );
					$bricksfly_counter_progress += 1;
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					update_option( 'bricksfly_template_import_progress', [
						'type'        => 'single',
						'total_items' => $total_init,
						'title'       => $temp_title,
						'progress'    => $bricksfly_counter_progress,
						'data'        => [ 'Posts: ' . $bricksfly_counter_progress ],
					] );
					$status = $this->process_author( $parsed['data'], $parsed['meta'] );
					if ( is_wp_error( $status ) ) {
						$this->log_error( $status );
					}
					$reader->next();
					break;

				case 'wp:category':
					$node   = $reader->expand();
					$parsed = $this->parse_term_node( $node, 'category' );
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					$bricksfly_counter_progress += 1;
					update_option( 'bricksfly_template_import_progress', [
						'type'        => 'single',
						'total_items' => $total_init,
						'title'       => $temp_title,
						'progress'    => $bricksfly_counter_progress,
						'data'        => [ 'Category: ' . $bricksfly_counter_progress ],
					] );
					$status = $this->process_term( $parsed['data'], $parsed['meta'] );
					$reader->next();
					break;

				case 'wp:tag':
					$node   = $reader->expand();
					$parsed = $this->parse_term_node( $node, 'tag' );
					$bricksfly_counter_progress += 1;
					update_option( 'bricksfly_template_import_progress', [
						'type'        => 'single',
						'total_items' => $total_init,
						'title'       => $temp_title,
						'progress'    => $bricksfly_counter_progress,
						'data'        => [ 'Terms: ' . $bricksfly_counter_progress ],
					] );
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					$status = $this->process_term( $parsed['data'], $parsed['meta'] );
					$reader->next();
					break;

				case 'wp:term':
					$node   = $reader->expand();
					$parsed = $this->parse_term_node( $node );
					$bricksfly_counter_progress += 1;
					if ( is_wp_error( $parsed ) ) {
						$this->log_error( $parsed );
						$reader->next();
						break;
					}
					update_option( 'bricksfly_template_import_progress', [
						'type'        => 'single',
						'progress'    => $bricksfly_counter_progress,
						'title'       => $temp_title,
						'total_items' => $total_init,
						'data'        => [ 'Terms: ' . $bricksfly_counter_progress ],
					] );
					$status = $this->process_term( $parsed['data'], $parsed['meta'] );
					$reader->next();
					break;

				default:
					break;
			}
		}

		$this->post_process();

		if ( $this->options['aggressive_url_search'] ) {
			$this->replace_attachment_urls_in_content();
			$bricksfly_counter_progress += 1;
		}
		$this->bricksfly_remap_featured_images();
		$this->import_end();
	}

	protected function log_error( WP_Error $error ) {
		$this->logger->warning( $error->get_error_message() );
	}

	protected function import_start( $file ) {
		if ( ! is_file( $file ) ) {
			return new WP_Error( 'bricksfly_importer.file_missing', __( 'The file does not exist, please try again.', 'bricksfly-elements-for-bricks' ) );
		}

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );
		wp_suspend_cache_invalidation( true );

		if ( $this->options['prefill_existing_posts'] ) {
			$this->prefill_existing_posts();
		}
		if ( $this->options['prefill_existing_comments'] ) {
			$this->prefill_existing_comments();
		}
		if ( $this->options['prefill_existing_terms'] ) {
			$this->prefill_existing_terms();
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		do_action( 'bricksfly_import_start' );
	}

	protected function import_end() {
		wp_suspend_cache_invalidation( false );
		wp_cache_flush();

		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );

		flush_rewrite_rules();

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		do_action( 'bricksfly_import_end' );
	}

	public function set_user_mapping( $mapping ) {
		foreach ( $mapping as $map ) {
			if ( empty( $map['old_slug'] ) || empty( $map['old_id'] ) || empty( $map['new_id'] ) ) {
				$this->logger->warning( __( 'Invalid author mapping', 'bricksfly-elements-for-bricks' ) );
				continue;
			}

			$old_slug = $map['old_slug'];
			$old_id   = $map['old_id'];
			$new_id   = $map['new_id'];

			$this->mapping['user'][ $old_id ]        = $new_id;
			$this->mapping['user_slug'][ $old_slug ] = $new_id;
		}
	}

	public function set_user_slug_overrides( $overrides ) {
		foreach ( $overrides as $original => $renamed ) {
			$this->user_slug_override[ $original ] = $renamed;
		}
	}

	protected function parse_post_node( $node ) {
		$data     = array();
		$meta     = array();
		$comments = array();
		$terms    = array();

		foreach ( $node->childNodes as $child ) {
			if ( $child->nodeType !== XML_ELEMENT_NODE ) {
				continue;
			}

			switch ( $child->tagName ) {
				case 'wp:post_type':      $data['post_type']      = $child->textContent; break;
				case 'title':             $data['post_title']     = $child->textContent; break;
				case 'guid':              $data['guid']           = $child->textContent; break;
				case 'dc:creator':        $data['post_author']    = $child->textContent; break;
				case 'content:encoded':   $data['post_content']   = $child->textContent; break;
				case 'excerpt:encoded':   $data['post_excerpt']   = $child->textContent; break;
				case 'wp:post_id':        $data['post_id']        = $child->textContent; break;
				case 'wp:post_date':      $data['post_date']      = $child->textContent; break;
				case 'wp:post_date_gmt':  $data['post_date_gmt']  = $child->textContent; break;
				case 'wp:comment_status': $data['comment_status'] = $child->textContent; break;
				case 'wp:ping_status':    $data['ping_status']    = $child->textContent; break;
				case 'wp:post_name':      $data['post_name']      = $child->textContent; break;
				case 'wp:status':
					$data['post_status'] = $child->textContent;
					if ( $data['post_status'] === 'auto-draft' ) {
						return new WP_Error( 'bricksfly_importer.post.cannot_import_draft', __( 'Cannot import auto-draft posts', 'bricksfly-elements-for-bricks' ), $data );
					}
					break;
				case 'wp:post_parent':    $data['post_parent']    = $child->textContent; break;
				case 'wp:menu_order':     $data['menu_order']     = $child->textContent; break;
				case 'wp:post_password':  $data['post_password']  = $child->textContent; break;
				case 'wp:is_sticky':      $data['is_sticky']      = $child->textContent; break;
				case 'wp:attachment_url':  $data['attachment_url'] = $child->textContent; break;
				case 'wp:postmeta':
					$meta_item = $this->parse_meta_node( $child );
					if ( ! empty( $meta_item ) ) {
						$meta[] = $meta_item;
					}
					break;
				case 'wp:comment':
					$comment_item = $this->parse_comment_node( $child );
					if ( ! empty( $comment_item ) ) {
						$comments[] = $comment_item;
					}
					break;
				case 'category':
					$term_item = $this->parse_category_node( $child );
					if ( ! empty( $term_item ) ) {
						$terms[] = $term_item;
					}
					break;
			}
		}

		return compact( 'data', 'meta', 'comments', 'terms' );
	}

	protected function process_post( $data, $meta, $comments, $terms ) {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		$data = apply_filters( 'bricksfly_importer.pre_process.post', $data, $meta, $comments, $terms );
		if ( empty( $data ) ) {
			return false;
		}

		$original_id = isset( $data['post_id'] )     ? (int) $data['post_id']     : 0;
		$parent_id   = isset( $data['post_parent'] ) ? (int) $data['post_parent'] : 0;

		if ( isset( $this->mapping['post'][ $original_id ] ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $data['post_type'] );
		if ( ! $post_type_object ) {
			return false;
		}

		$post_exists = $this->post_exists( $data );
		if ( $post_exists ) {
			$this->process_comments( $comments, $original_id, $data, $post_exists );
			do_action('bricksfly_import_existing_post', $post_exists, $original_id, $data, $data );
			return false;
		}

		$requires_remapping = false;
		if ( $parent_id ) {
			if ( isset( $this->mapping['post'][ $parent_id ] ) ) {
				$data['post_parent'] = $this->mapping['post'][ $parent_id ];
			} else {
				$meta[]             = array( 'key' => '_bricksfly_import_parent', 'value' => $parent_id );
				$requires_remapping = true;
				$data['post_parent'] = 0;
			}
		}

		$author = sanitize_user( $data['post_author'], true );
		if ( empty( $author ) ) {
			$data['post_author'] = $this->options['default_author'];
		} elseif ( isset( $this->mapping['user_slug'][ $author ] ) ) {
			$data['post_author'] = $this->mapping['user_slug'][ $author ];
		} else {
			$meta[]              = array( 'key' => '_bricksfly_import_user_slug', 'value' => $author );
			$requires_remapping  = true;
			$data['post_author'] = (int) get_current_user_id();
		}

		if ( preg_match( self::REGEX_HAS_ATTACHMENT_REFS, $data['post_content'] ) ) {
			$meta[]             = array( 'key' => '_bricksfly_import_has_attachment_refs', 'value' => true );
			$requires_remapping = true;
		}

		$postdata = array( 'import_id' => $data['post_id'] );
		$allowed  = array(
			'post_author' => true, 'post_date' => true, 'post_date_gmt' => true,
			'post_content' => true, 'post_excerpt' => true, 'post_title' => true,
			'post_status' => true, 'post_name' => true, 'comment_status' => true,
			'ping_status' => true, 'guid' => true, 'post_parent' => true,
			'menu_order' => true, 'post_type' => true, 'post_password' => true,
		);
		foreach ( $data as $key => $value ) {
			if ( ! isset( $allowed[ $key ] ) ) { continue; }
			$postdata[ $key ] = $data[ $key ];
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		$postdata = apply_filters( 'wp_import_post_data_processed', wp_slash( $postdata ), $data );

		if ( 'attachment' === $postdata['post_type'] ) {
			$remote_url = ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'];
			if ( ! $this->options['fetch_attachments'] ) {
				update_option( 'bricksfly_template_import_state', __( 'fetching attachments disabled', 'bricksfly-elements-for-bricks' ) );
				return false;
			}
			$post_id = $this->process_attachment( $postdata, $meta, $remote_url );
		} else {
			$post_id = wp_insert_post( $postdata, true );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			do_action( 'wp_import_insert_post', $post_id, $original_id, $postdata, $data );
		}

		if ( is_wp_error( $post_id ) ) {
			$this->logger->debug( $post_id->get_error_message() );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			do_action( 'bricksfly_importer.process_failed.post', $post_id, $data, $meta, $comments, $terms );
			return false;
		}

		if ( $data['is_sticky'] === '1' ) {
			stick_post( $post_id );
		}

		$this->mapping['post'][ $original_id ] = (int) $post_id;
		if ( $requires_remapping ) {
			$this->requires_remapping['post'][ $post_id ] = true;
		}
		$this->mark_post_exists( $data, $post_id );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		$terms = apply_filters( 'wp_import_post_terms', $terms, $post_id, $data );

		if ( ! empty( $terms ) ) {
			$term_ids = array();
			foreach ( $terms as $term ) {
				$taxonomy = $term['taxonomy'];
				$key      = sha1( $taxonomy . ':' . $term['slug'] );

				if ( isset( $this->mapping['term'][ $key ] ) ) {
					$term_ids[ $taxonomy ][] = (int) $this->mapping['term'][ $key ];
				} else {
					if ( 'post_format' === $taxonomy ) {
						$term_exists = term_exists( $term['slug'], $taxonomy );
						$term_id     = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
						if ( empty( $term_id ) ) {
							$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
							if ( ! is_wp_error( $t ) ) {
								$term_id                         = $t['term_id'];
								$this->mapping['term'][ $key ]   = $term_id;
							} else {
								continue;
							}
						}
						if ( ! empty( $term_id ) ) {
							$term_ids[ $taxonomy ][] = intval( $term_id );
						}
					} else {
						$meta[]             = array( 'key' => '_bricksfly_import_term', 'value' => $term );
						$requires_remapping = true;
					}
				}
			}

			foreach ( $term_ids as $tax => $ids ) {
				$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
				do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $data );
			}
		}

		$this->process_comments( $comments, $post_id, $data );
		$this->process_post_meta( $meta, $post_id, $data );

		if ( 'nav_menu_item' === $data['post_type'] ) {
			$this->process_menu_item_meta( $post_id, $data, $meta );
		}

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		do_action( 'bricksfly_importer.processed.post', $post_id, $data, $meta, $comments, $terms );
	}

	protected function process_menu_item_meta( $post_id, $data, $meta ) {
		$item_type          = get_post_meta( $post_id, '_menu_item_type', true );
		$original_object_id = get_post_meta( $post_id, '_menu_item_object_id', true );
		$object_id          = null;

		$this->logger->debug( sprintf( 'Processing menu item %s', $item_type ) );

		$requires_remapping = false;
		switch ( $item_type ) {
			case 'taxonomy':
				if ( isset( $this->mapping['term_id'][ $original_object_id ] ) ) {
					$object_id = $this->mapping['term_id'][ $original_object_id ];
				} else {
					add_post_meta( $post_id, '_bricksfly_import_menu_item', wp_slash( $original_object_id ) );
					$requires_remapping = true;
				}
				break;
			case 'post_type':
				if ( isset( $this->mapping['post'][ $original_object_id ] ) ) {
					$object_id = $this->mapping['post'][ $original_object_id ];
				} else {
					add_post_meta( $post_id, '_bricksfly_import_menu_item', wp_slash( $original_object_id ) );
					$requires_remapping = true;
				}
				break;
			case 'custom':
				$object_id = $post_id;
				break;
			default:
				$this->missing_menu_items[] = $data;
				break;
		}

		if ( $requires_remapping ) {
			$this->requires_remapping['post'][ $post_id ] = true;
		}

		if ( empty( $object_id ) ) {
			return;
		}

		$this->logger->debug( sprintf( 'Menu item %d mapped to %d', $original_object_id, $object_id ) );
		update_post_meta( $post_id, '_menu_item_object_id', wp_slash( $object_id ) );
	}

	protected function process_attachment( $post, $meta, $remote_url ) {
		$post['upload_date'] = $post['post_date'];
		foreach ( $meta as $meta_item ) {
			if ( $meta_item['key'] !== '_wp_attached_file' ) { continue; }
			if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta_item['value'], $matches ) ) {
				$post['upload_date'] = $matches[0];
			}
			break;
		}

		if ( preg_match( '|^/[\w\W]+$|', $remote_url ) ) {
			$remote_url = rtrim( $this->base_url, '/' ) . $remote_url;
		}

		$upload = $this->fetch_remote_file( $remote_url, $post );
		if ( is_wp_error( $upload ) ) {
			return $upload;
		}

		$info = wp_check_filetype( $upload['file'] );
		if ( ! $info ) {
			return new WP_Error( 'attachment_processing_error', __( 'Invalid file type', 'bricksfly-elements-for-bricks' ) );
		}

		$post['post_mime_type'] = $info['type'];

		if ( $this->options['update_attachment_guids'] ) {
			$post['guid'] = $upload['url'];
		}

		$post_id = wp_insert_attachment( $post, $upload['file'] );
		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		$attachment_metadata = wp_generate_attachment_metadata( $post_id, $upload['file'] );
		wp_update_attachment_metadata( $post_id, $attachment_metadata );

		$this->url_remap[ $remote_url ] = $upload['url'];

		if ( substr( $remote_url, 0, 8 ) === 'https://' ) {
			$insecure_url                       = 'http' . substr( $remote_url, 5 );
			$this->url_remap[ $insecure_url ]   = $upload['url'];
		}

		return $post_id;
	}

	protected function parse_meta_node( $node ) {
		foreach ( $node->childNodes as $child ) {
			if ( $child->nodeType !== XML_ELEMENT_NODE ) { continue; }
			switch ( $child->tagName ) {
				case 'wp:meta_key':   $key   = $child->textContent; break;
				case 'wp:meta_value': $value = $child->textContent; break;
			}
		}
		if ( empty( $key ) || ! isset( $value ) ) { return null; }
		return compact( 'key', 'value' );
	}

	protected function process_post_meta( $meta, $post_id, $post ) {
		if ( empty( $meta ) ) { return true; }

		foreach ( $meta as $meta_item ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			$meta_item = apply_filters( 'bricksfly_importer.pre_process.post_meta', $meta_item, $post_id );
			if ( empty( $meta_item ) ) { return false; }

			$key   = apply_filters('bricksfly_import_post_meta_key', $meta_item['key'], $post_id, $post );
			$value = false;

			if ( '_edit_last' === $key ) {
				$value = intval( $meta_item['value'] );
				if ( ! isset( $this->mapping['user'][ $value ] ) ) { continue; }
				$value = $this->mapping['user'][ $value ];
			}

			if ( $key ) {
				if ( ! $value ) {
					$value = maybe_unserialize( $meta_item['value'] );
				}
				add_post_meta( $post_id, wp_slash( $key ), wp_slash( $value ) );
				do_action('bricksfly_import_post_meta', $post_id, $key, $value );
				if ( '_thumbnail_id' === $key ) {
					$this->featured_images[ $post_id ] = (int) $value;
				}
			}
		}

		return true;
	}

	protected function parse_comment_node( $node ) {
		$data = array( 'commentmeta' => array() );
		foreach ( $node->childNodes as $child ) {
			if ( $child->nodeType !== XML_ELEMENT_NODE ) { continue; }
			switch ( $child->tagName ) {
				case 'wp:comment_id':           $data['comment_id']           = $child->textContent; break;
				case 'wp:comment_author':        $data['comment_author']       = $child->textContent; break;
				case 'wp:comment_author_email':  $data['comment_author_email'] = $child->textContent; break;
				case 'wp:comment_author_IP':     $data['comment_author_IP']    = $child->textContent; break;
				case 'wp:comment_author_url':    $data['comment_author_url']   = $child->textContent; break;
				case 'wp:comment_user_id':       $data['comment_user_id']      = $child->textContent; break;
				case 'wp:comment_date':          $data['comment_date']         = $child->textContent; break;
				case 'wp:comment_date_gmt':      $data['comment_date_gmt']     = $child->textContent; break;
				case 'wp:comment_content':       $data['comment_content']      = $child->textContent; break;
				case 'wp:comment_approved':       $data['comment_approved']    = $child->textContent; break;
				case 'wp:comment_type':          $data['comment_type']         = $child->textContent; break;
				case 'wp:comment_parent':        $data['comment_parent']       = $child->textContent; break;
				case 'wp:commentmeta':
					$meta_item = $this->parse_meta_node( $child );
					if ( ! empty( $meta_item ) ) { $data['commentmeta'][] = $meta_item; }
					break;
			}
		}
		return $data;
	}

	protected function process_comments( $comments, $post_id, $post, $post_exists = false ) {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		$comments = apply_filters( 'wp_import_post_comments', $comments, $post_id, $post );
		if ( empty( $comments ) ) { return 0; }

		$num_comments = 0;
		usort( $comments, array( $this, 'sort_comments_by_id' ) );

		foreach ( $comments as $key => $comment ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			$comment = apply_filters( 'bricksfly_importer.pre_process.comment', $comment, $post_id );
			if ( empty( $comment ) ) { return false; }

			$original_id = isset( $comment['comment_id'] )      ? (int) $comment['comment_id']      : 0;
			$parent_id   = isset( $comment['comment_parent'] )  ? (int) $comment['comment_parent']  : 0;
			$author_id   = isset( $comment['comment_user_id'] ) ? (int) $comment['comment_user_id'] : 0;

			if ( $post_exists ) {
				$existing = $this->comment_exists( $comment );
				if ( $existing ) {
					$this->mapping['comment'][ $original_id ] = $existing;
					continue;
				}
			}

			$meta = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
			unset( $comment['commentmeta'] );

			$requires_remapping = false;
			if ( $parent_id ) {
				if ( isset( $this->mapping['comment'][ $parent_id ] ) ) {
					$comment['comment_parent'] = $this->mapping['comment'][ $parent_id ];
				} else {
					$meta[]             = array( 'key' => '_bricksfly_import_parent', 'value' => $parent_id );
					$requires_remapping = true;
					$comment['comment_parent'] = 0;
				}
			}

			if ( $author_id ) {
				if ( isset( $this->mapping['user'][ $author_id ] ) ) {
					$comment['user_id'] = $this->mapping['user'][ $author_id ];
				} else {
					$meta[]             = array( 'key' => '_bricksfly_import_user', 'value' => $author_id );
					$requires_remapping = true;
					$comment['user_id'] = 0;
				}
			}

			$comment['comment_post_ID'] = $post_id;
			$comment    = wp_filter_comment( $comment );
			$comment_id = wp_insert_comment( wp_slash( $comment ) );
			$this->mapping['comment'][ $original_id ] = $comment_id;
			if ( $requires_remapping ) {
				$this->requires_remapping['comment'][ $comment_id ] = true;
			}
			$this->mark_comment_exists( $comment, $comment_id );

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			do_action( 'wp_import_insert_comment', $comment_id, $comment, $post_id, $post );

			foreach ( $meta as $meta_item ) {
				$value = maybe_unserialize( $meta_item['value'] );
				add_comment_meta( $comment_id, wp_slash( $meta_item['key'] ), wp_slash( $value ) );
			}

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			do_action( 'bricksfly_importer.processed.comment', $comment_id, $comment, $meta, $post_id );
			$num_comments++;
		}

		return $num_comments;
	}

	protected function parse_category_node( $node ) {
		$data = array( 'taxonomy' => 'category' );
		if ( $node->hasAttribute( 'domain' ) )   { $data['taxonomy'] = $node->getAttribute( 'domain' ); }
		if ( $node->hasAttribute( 'nicename' ) )  { $data['slug']     = $node->getAttribute( 'nicename' ); }
		$data['name'] = $node->textContent;
		if ( empty( $data['slug'] ) ) { return null; }
		if ( $data['taxonomy'] === 'tag' ) { $data['taxonomy'] = 'post_tag'; }
		return $data;
	}

	public static function sort_comments_by_id( $a, $b ) {
		if ( empty( $a['comment_id'] ) ) { return 1; }
		if ( empty( $b['comment_id'] ) ) { return -1; }
		return $a['comment_id'] - $b['comment_id'];
	}

	protected function parse_author_node( $node ) {
		$data = array();
		$meta = array();
		foreach ( $node->childNodes as $child ) {
			if ( $child->nodeType !== XML_ELEMENT_NODE ) { continue; }
			switch ( $child->tagName ) {
				case 'wp:author_login':        $data['user_login']   = $child->textContent; break;
				case 'wp:author_id':           $data['ID']           = $child->textContent; break;
				case 'wp:author_email':        $data['user_email']   = $child->textContent; break;
				case 'wp:author_display_name': $data['display_name'] = $child->textContent; break;
				case 'wp:author_first_name':   $data['first_name']   = $child->textContent; break;
				case 'wp:author_last_name':    $data['last_name']    = $child->textContent; break;
			}
		}
		return compact( 'data', 'meta' );
	}

	protected function process_author( $data, $meta ) {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		$data = apply_filters( 'bricksfly_importer.pre_process.user', $data, $meta );
		if ( empty( $data ) ) { return false; }

		$original_id   = isset( $data['ID'] ) ? $data['ID'] : 0;
		$original_slug = $data['user_login'];

		if ( isset( $this->mapping['user'][ $original_id ] ) ) {
			$existing = $this->mapping['user'][ $original_id ];
			if ( ! isset( $this->mapping['user_slug'][ $original_slug ] ) ) {
				$this->mapping['user_slug'][ $original_slug ] = $existing;
			}
			return false;
		}
		if ( isset( $this->mapping['user_slug'][ $original_slug ] ) ) {
			$existing                                = $this->mapping['user_slug'][ $original_slug ];
			$this->mapping['user'][ $original_id ]   = $existing;
			return false;
		}

		$login = $original_slug;
		if ( isset( $this->user_slug_override[ $login ] ) ) {
			$login = $this->user_slug_override[ $login ];
		}

		$userdata = array(
			'user_login' => sanitize_user( $login, true ),
			'user_pass'  => wp_generate_password(),
		);

		$allowed = array( 'user_email' => true, 'display_name' => true, 'first_name' => true, 'last_name' => true );
		foreach ( $data as $key => $value ) {
			if ( ! isset( $allowed[ $key ] ) ) { continue; }
			$userdata[ $key ] = $data[ $key ];
		}

		$user_id = wp_insert_user( wp_slash( $userdata ) );
		if ( is_wp_error( $user_id ) ) {
			$this->logger->debug( $user_id->get_error_message() );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			do_action( 'bricksfly_importer.process_failed.user', $user_id, $userdata );
			return false;
		}

		if ( $original_id ) {
			$this->mapping['user'][ $original_id ] = $user_id;
		}
		$this->mapping['user_slug'][ $original_slug ] = $user_id;

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		do_action( 'bricksfly_importer.processed.user', $user_id, $userdata );
	}

	protected function parse_term_node( $node, $type = 'term' ) {
		$data = array();
		$meta = array();

		$tag_name = array(
			'id'          => 'wp:term_id',
			'taxonomy'    => 'wp:term_taxonomy',
			'slug'        => 'wp:term_slug',
			'parent'      => 'wp:term_parent',
			'name'        => 'wp:term_name',
			'description' => 'wp:term_description',
		);

		switch ( $type ) {
			case 'category':
				$tag_name['slug']        = 'wp:category_nicename';
				$tag_name['parent']      = 'wp:category_parent';
				$tag_name['name']        = 'wp:cat_name';
				$tag_name['description'] = 'wp:category_description';
				$tag_name['taxonomy']    = null;
				$data['taxonomy']        = 'category';
				break;
			case 'tag':
				$tag_name['slug']        = 'wp:tag_slug';
				$tag_name['parent']      = null;
				$tag_name['name']        = 'wp:tag_name';
				$tag_name['description'] = 'wp:tag_description';
				$tag_name['taxonomy']    = null;
				$data['taxonomy']        = 'post_tag';
				break;
		}

		foreach ( $node->childNodes as $child ) {
			if ( $child->nodeType !== XML_ELEMENT_NODE ) { continue; }
			$key = array_search( $child->tagName, $tag_name );
			if ( $key ) {
				$data[ $key ] = $child->textContent;
			} else if ( $child->tagName == 'wp:termmeta' ) {
				$meta_item = $this->parse_meta_node( $child );
				if ( ! empty( $meta_item ) ) { $meta[] = $meta_item; }
			}
		}
		if ( empty( $data['taxonomy'] ) ) { return null; }
		if ( $data['taxonomy'] === 'tag' ) { $data['taxonomy'] = 'post_tag'; }
		return compact( 'data', 'meta' );
	}

	protected function process_term( $data, $meta ) {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		$data = apply_filters( 'bricksfly_importer.pre_process.term', $data, $meta );
		if ( empty( $data ) ) { return false; }

		$original_id = isset( $data['id'] ) ? (int) $data['id'] : 0;
		$term_slug   = isset( $data['slug'] )   ? $data['slug']   : '';
		$parent_slug = isset( $data['parent'] ) ? $data['parent'] : '';

		$mapping_key = sha1( $data['taxonomy'] . ':' . $data['slug'] );
		$existing    = $this->term_exists( $data );
		if ( $existing ) {
			$this->mapping['term'][ $mapping_key ]      = $existing;
			$this->mapping['term_id'][ $original_id ]   = $existing;
			$this->mapping['term_slug'][ $term_slug ]   = $existing;
			return false;
		}
		if ( isset( $this->mapping['term'][ $mapping_key ] ) ) { return false; }

		$termdata = array();
		$allowed  = array( 'slug' => true, 'description' => true, 'parent' => true );

		$requires_remapping = false;
		if ( $parent_slug ) {
			if ( isset( $this->mapping['term_slug'][ $parent_slug ] ) ) {
				$data['parent'] = $this->mapping['term_slug'][ $parent_slug ];
			} else {
				$meta[]             = array( 'key' => '_bricksfly_import_parent', 'value' => $parent_slug );
				$requires_remapping = true;
				$data['parent']     = 0;
			}
		}

		foreach ( $data as $key => $value ) {
			if ( ! isset( $allowed[ $key ] ) ) { continue; }
			$termdata[ $key ] = $data[ $key ];
		}

		$result = wp_insert_term( $data['name'], $data['taxonomy'], $termdata );
		if ( is_wp_error( $result ) ) {
			$this->logger->debug( $result->get_error_message() );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			do_action( 'wp_import_insert_term_failed', $result, $data );
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			do_action( 'bricksfly_importer.process_failed.term', $result, $data, $meta );
			return false;
		}

		$term_id = $result['term_id'];

		$this->mapping['term'][ $mapping_key ]      = $term_id;
		$this->mapping['term_id'][ $original_id ]   = $term_id;
		$this->mapping['term_slug'][ $term_slug ]   = $term_id;

		if ( $requires_remapping ) {
			$this->requires_remapping['term'][ $term_id ] = $data['taxonomy'];
		}

		$this->process_term_meta( $meta, $term_id, $data );

		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		do_action( 'wp_import_insert_term', $term_id, $data );
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		do_action( 'bricksfly_importer.processed.term', $term_id, $data );
	}

	protected function process_term_meta( $meta, $term_id, $term ) {
		if ( empty( $meta ) ) { return true; }

		foreach ( $meta as $meta_item ) {
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			$meta_item = apply_filters( 'bricksfly_importer.pre_process.term_meta', $meta_item, $term_id );
			if ( empty( $meta_item ) ) { continue; }

			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
			$key   = apply_filters( 'bricksfly_import_term_meta_key', $meta_item['key'], $term_id, $term );
			$value = false;
			if ( $key ) {
				if ( ! $value ) { $value = maybe_unserialize( $meta_item['value'] ); }
				$result = add_term_meta( $term_id, $key, $value );
				if ( is_wp_error( $result ) ) {
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
					do_action( 'bricksfly_importer.process_failed.termmeta', $result, $meta_item, $term_id, $term );
				}
				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
				do_action( 'bricksfly_import_term_meta', $term_id, $key, $value );
			}
		}
		return true;
	}

	protected function fetch_remote_file( $url, $post ) {
		$file_name = basename( $url );
		$upload    = wp_upload_bits( $file_name, null, '', $post['upload_date'] );
		if ( $upload['error'] ) { return new WP_Error( 'upload_dir_error', $upload['error'] ); }

		$response = wp_remote_get( $url, array( 'stream' => true, 'filename' => $upload['file'] ) );
		if ( is_wp_error( $response ) ) { wp_delete_file( $upload['file'] ); return $response; }

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code !== 200 ) {
			wp_delete_file( $upload['file'] );
			return new WP_Error( 'import_file_error', sprintf(
				/* translators: 1: HTTP status code, 2: HTTP status text, 3: requested URL. */
				__( 'Remote server returned %1$d %2$s for %3$s', 'bricksfly-elements-for-bricks' ),
				$code, get_status_header_desc( $code ), $url
			) );
		}

		$filesize = filesize( $upload['file'] );
		if ( 0 === $filesize ) { wp_delete_file( $upload['file'] ); return new WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'bricksfly-elements-for-bricks' ) ); }

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			wp_delete_file( $upload['file'] );
			return new WP_Error(
				'import_file_error',
				sprintf(
					/* translators: %s: human-readable maximum allowed file size (e.g. "5 MB"). */
					__( 'Remote file is too large, limit is %s', 'bricksfly-elements-for-bricks' ),
					size_format( $max_size )
				)
			);
		}

		return $upload;
	}

	protected function post_process() {
		if ( ! empty( $this->requires_remapping['post'] ) ) {
			update_option( 'bricksfly_template_import_state', esc_html__( 'Processing Posts', 'bricksfly-elements-for-bricks' ) );
			$this->post_process_posts( $this->requires_remapping['post'] );
		}
		if ( ! empty( $this->requires_remapping['comment'] ) ) {
			update_option( 'bricksfly_template_import_state', esc_html__( 'Processing Comments', 'bricksfly-elements-for-bricks' ) );
			$this->post_process_comments( $this->requires_remapping['comment'] );
		}
		if ( ! empty( $this->requires_remapping['term'] ) ) {
			update_option( 'bricksfly_template_import_state', esc_html__( 'Processing Terms', 'bricksfly-elements-for-bricks' ) );
			$this->post_process_terms( $this->requires_remapping['term'] );
		}
	}

	protected function post_process_posts( $todo ) {
		foreach ( $todo as $post_id => $_ ) {
			$data      = array();
			$parent_id = get_post_meta( $post_id, '_bricksfly_import_parent', true );
			if ( ! empty( $parent_id ) ) {
				if ( isset( $this->mapping['post'][ $parent_id ] ) ) {
					$data['post_parent'] = $this->mapping['post'][ $parent_id ];
				}
			}
			$author_slug = get_post_meta( $post_id, '_bricksfly_import_user_slug', true );
			if ( ! empty( $author_slug ) ) {
				if ( isset( $this->mapping['user_slug'][ $author_slug ] ) ) {
					$data['post_author'] = $this->mapping['user_slug'][ $author_slug ];
				}
			}
			$has_attachments = get_post_meta( $post_id, '_bricksfly_import_has_attachment_refs', true );
			if ( ! empty( $has_attachments ) ) {
				$post        = get_post( $post_id );
				$content     = $post->post_content;
				$new_content = str_replace( array_keys( $this->url_remap ), $this->url_remap, $content );
				if ( $new_content !== $content ) { $data['post_content'] = $new_content; }
			}
			if ( get_post_type( $post_id ) === 'nav_menu_item' ) { $this->post_process_menu_item( $post_id ); }
			if ( empty( $data ) ) { continue; }

			$data['ID'] = $post_id;
			$result     = wp_update_post( $data, true );
			if ( is_wp_error( $result ) ) { continue; }

			delete_post_meta( $post_id, '_bricksfly_import_parent' );
			delete_post_meta( $post_id, '_bricksfly_import_user_slug' );
			delete_post_meta( $post_id, '_bricksfly_import_has_attachment_refs' );
		}
	}

	protected function post_process_menu_item( $post_id ) {
		$menu_object_id = get_post_meta( $post_id, '_bricksfly_import_menu_item', true );
		if ( empty( $menu_object_id ) ) { return; }

		$menu_item_type = get_post_meta( $post_id, '_menu_item_type', true );
		switch ( $menu_item_type ) {
			case 'taxonomy':
				if ( isset( $this->mapping['term_id'][ $menu_object_id ] ) ) { $menu_object = $this->mapping['term_id'][ $menu_object_id ]; }
				break;
			case 'post_type':
				if ( isset( $this->mapping['post'][ $menu_object_id ] ) ) { $menu_object = $this->mapping['post'][ $menu_object_id ]; }
				break;
			default:
				return;
		}

		if ( ! empty( $menu_object ) ) {
			update_post_meta( $post_id, '_menu_item_object_id', wp_slash( $menu_object ) );
		}

		delete_post_meta( $post_id, '_bricksfly_import_menu_item' );
	}

	protected function post_process_comments( $todo ) {
		foreach ( $todo as $comment_id => $_ ) {
			$data      = array();
			$parent_id = get_comment_meta( $comment_id, '_bricksfly_import_parent', true );
			if ( ! empty( $parent_id ) && isset( $this->mapping['comment'][ $parent_id ] ) ) {
				$data['comment_parent'] = $this->mapping['comment'][ $parent_id ];
			}
			$author_id = get_comment_meta( $comment_id, '_bricksfly_import_user', true );
			if ( ! empty( $author_id ) && isset( $this->mapping['user'][ $author_id ] ) ) {
				$data['user_id'] = $this->mapping['user'][ $author_id ];
			}
			if ( empty( $data ) ) { continue; }
			$data['comment_ID'] = $comment_id;
			$result             = wp_update_comment( wp_slash( $data ) );
			if ( ! empty( $result ) ) {
				delete_comment_meta( $comment_id, '_bricksfly_import_parent' );
				delete_comment_meta( $comment_id, '_bricksfly_import_user' );
			}
		}
	}

	protected function post_process_terms( $terms_to_be_remapped ) {
		$this->mapping['term_slug']['top'] = 0;
		foreach ( $terms_to_be_remapped as $termid => $term_taxonomy ) {
			if ( empty( $termid ) || ! is_numeric( $termid ) ) { continue; }
			$term_id = (int) $termid;
			if ( empty( $term_taxonomy ) ) { continue; }

			$parent_slug = get_term_meta( $term_id, '_bricksfly_import_parent', true );
			if ( empty( $parent_slug ) ) { continue; }
			if ( ! isset( $this->mapping['term_slug'][ $parent_slug ] ) || ! is_numeric( $this->mapping['term_slug'][ $parent_slug ] ) ) { continue; }

			$mapped_parent  = (int) $this->mapping['term_slug'][ $parent_slug ];
			$termattributes = get_term_by( 'id', $term_id, $term_taxonomy, ARRAY_A );
			if ( empty( $termattributes ) ) { continue; }
			if ( isset( $termattributes['parent'] ) && $termattributes['parent'] == $mapped_parent ) {
				delete_term_meta( $term_id, '_bricksfly_import_parent' );
				continue;
			}
			$termattributes['parent'] = $mapped_parent;
			$result                   = wp_update_term( $term_id, $termattributes['taxonomy'], $termattributes );
			if ( ! is_wp_error( $result ) ) {
				delete_term_meta( $term_id, '_bricksfly_import_parent' );
			}
		}
	}

	protected function replace_attachment_urls_in_content() {
		global $wpdb;
		uksort( $this->url_remap, array( $this, 'cmpr_strlen' ) );
		foreach ( $this->url_remap as $from_url => $to_url ) {
			$wpdb->query( $wpdb->prepare(
				"UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s) WHERE post_content LIKE %s",
				$from_url, $to_url, '%' . $wpdb->esc_like( $from_url ) . '%'
			) );
			$wpdb->query( $wpdb->prepare(
				"UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key = %s AND meta_value LIKE %s",
				$from_url, $to_url, 'enclosure', '%' . $wpdb->esc_like( $from_url ) . '%'
			) );
		}
	}

	function bricksfly_remap_featured_images() {
		if ( empty( $this->featured_images ) ) { return; }
		update_option( 'bricksfly_template_import_state', esc_html__( 'Starting remapping of featured images', 'bricksfly-elements-for-bricks' ) );
		foreach ( $this->featured_images as $post_id => $value ) {
			if ( isset( $this->mapping['post'][ $value ] ) ) {
				$new_id = $this->mapping['post'][ $value ];
				if ( $new_id !== $value ) { update_post_meta( $post_id, '_thumbnail_id', $new_id ); }
			}
		}
	}

	public function is_valid_meta_key( $key ) {
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) ) { return false; }
		return $key;
	}

	protected function max_attachment_size() {
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress Importer API hook.
		return apply_filters( 'bricksfly_import_attachment_size_limit', 0 );
	}

	function bump_request_timeout( $val ) { return 60; }

	function cmpr_strlen( $a, $b ) { return strlen( $b ) - strlen( $a ); }

	protected function prefill_existing_posts() {
		global $wpdb;
		$posts = $wpdb->get_results(
			$wpdb->prepare( "SELECT ID, guid FROM {$wpdb->posts} WHERE post_type != %s", '' )
		);
		foreach ( $posts as $item ) { $this->exists['post'][ $item->guid ] = $item->ID; }
	}

	protected function post_exists( $data ) {
		$exists_key = $data['guid'];
		if ( $this->options['prefill_existing_posts'] ) {
			$exists_key = htmlentities( $exists_key );
			return isset( $this->exists['post'][ $exists_key ] ) ? $this->exists['post'][ $exists_key ] : false;
		}
		if ( isset( $this->exists['post'][ $exists_key ] ) ) { return $this->exists['post'][ $exists_key ]; }
		$exists = post_exists( $data['post_title'], $data['post_content'], $data['post_date'] );
		$this->exists['post'][ $exists_key ] = $exists;
		return $exists;
	}

	protected function mark_post_exists( $data, $post_id ) {
		$this->exists['post'][ $data['guid'] ] = $post_id;
	}

	protected function prefill_existing_comments() {
		global $wpdb;
		update_option( 'bricksfly_template_import_state', esc_html__( 'Comment checking', 'bricksfly-elements-for-bricks' ) );
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table-name interpolation only, no user input.
		$posts = $wpdb->get_results( "SELECT comment_ID, comment_author, comment_date FROM {$wpdb->comments}" );
		foreach ( $posts as $item ) {
			$exists_key                              = sha1( $item->comment_author . ':' . $item->comment_date );
			$this->exists['comment'][ $exists_key ]  = $item->comment_ID;
		}
	}

	protected function comment_exists( $data ) {
		$exists_key = sha1( $data['comment_author'] . ':' . $data['comment_date'] );
		if ( $this->options['prefill_existing_comments'] ) {
			return isset( $this->exists['comment'][ $exists_key ] ) ? $this->exists['comment'][ $exists_key ] : false;
		}
		if ( isset( $this->exists['comment'][ $exists_key ] ) ) { return $this->exists['comment'][ $exists_key ]; }
		$exists = comment_exists( $data['comment_author'], $data['comment_date'] );
		$this->exists['comment'][ $exists_key ] = $exists;
		return $exists;
	}

	protected function mark_comment_exists( $data, $comment_id ) {
		$this->exists['comment'][ sha1( $data['comment_author'] . ':' . $data['comment_date'] ) ] = $comment_id;
	}

	protected function prefill_existing_terms() {
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table-name interpolation only, no user input.
		$terms = $wpdb->get_results( "SELECT t.term_id, tt.taxonomy, t.slug FROM {$wpdb->terms} AS t JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id" );
		foreach ( $terms as $item ) {
			$exists_key                            = sha1( $item->taxonomy . ':' . $item->slug );
			$this->exists['term'][ $exists_key ]   = $item->term_id;
		}
	}

	protected function term_exists( $data ) {
		$exists_key = sha1( $data['taxonomy'] . ':' . $data['slug'] );
		if ( $this->options['prefill_existing_terms'] ) {
			return isset( $this->exists['term'][ $exists_key ] ) ? $this->exists['term'][ $exists_key ] : false;
		}
		if ( isset( $this->exists['term'][ $exists_key ] ) ) { return $this->exists['term'][ $exists_key ]; }
		$exists = term_exists( $data['slug'], $data['taxonomy'] );
		if ( is_array( $exists ) ) { $exists = $exists['term_id']; }
		$this->exists['term'][ $exists_key ] = $exists;
		return $exists;
	}

	protected function mark_term_exists( $data, $term_id ) {
		$this->exists['term'][ sha1( $data['taxonomy'] . ':' . $data['slug'] ) ] = $term_id;
	}
}
