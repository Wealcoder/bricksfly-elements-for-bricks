<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Video_Story extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-video-story';
	public $icon         = 'ti-video-clapper aab-element-marker';
	public $css_selector = '.aab--video-story';
	public $scripts      = array( 'bricksflyVideoStory' );

	public function get_label() {
		return esc_html__( 'Video Story', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'video', 'story', 'posts', 'grid', 'media' );
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'aab-video-story',
			BRICKSFLY_URL . 'public/build/elements/video-story.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-video-story',
			BRICKSFLY_URL . 'public/build/elements/video-story.js',
			array(),
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['query'] = array(
			'title' => esc_html__( 'Query', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['settings'] = array(
			'title' => esc_html__( 'Settings', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['title_content'] = array(
			'title' => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['excerpt_content'] = array(
			'title' => esc_html__( 'Excerpt', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['taxonomy_content'] = array(
			'title' => esc_html__( 'Taxonomy', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		// Style
		$this->control_groups['layout_style'] = array(
			'title' => esc_html__( 'Layout', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['video_style'] = array(
			'title' => esc_html__( 'Video', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['content_style'] = array(
			'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['title_style'] = array(
			'title' => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['excerpt_style'] = array(
			'title' => esc_html__( 'Excerpt', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['taxonomy_style'] = array(
			'title' => esc_html__( 'Taxonomy', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['meta_style'] = array(
			'title' => esc_html__( 'Meta', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
	}

	public function set_controls() {

		// Query
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$pt_options = array();
		foreach ( $post_types as $pt ) {
			$pt_options[ $pt->name ] = $pt->label;
		}

		$this->controls['post_type'] = array(
			'group'   => 'query',
			'label'   => esc_html__( 'Source', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => $pt_options,
			'default' => 'post',
		);

		$this->controls['post_order_by'] = array(
			'group'   => 'query',
			'label'   => esc_html__( 'Order By', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'date'          => esc_html__( 'Date', 'bricksfly-elements-for-bricks' ),
				'title'         => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
				'menu_order'    => esc_html__( 'Menu Order', 'bricksfly-elements-for-bricks' ),
				'modified'      => esc_html__( 'Last Modified', 'bricksfly-elements-for-bricks' ),
				'comment_count' => esc_html__( 'Comment Count', 'bricksfly-elements-for-bricks' ),
				'rand'          => esc_html__( 'Random', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'date',
		);

		$this->controls['post_order'] = array(
			'group'   => 'query',
			'label'   => esc_html__( 'Order', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'asc'  => 'ASC',
				'desc' => 'DESC',
			),
			'default' => 'desc',
		);

		// Settings
		$this->controls['posts_per_page'] = array(
			'group'   => 'settings',
			'label'   => esc_html__( 'Posts Per Page', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'default' => 6,
		);

		$this->controls['show_title'] = array(
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Title', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['show_excerpt'] = array(
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Excerpt', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['show_taxonomy'] = array(
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Taxonomy', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['show_meta'] = array(
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Meta', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		// Title content
		$this->controls['title_tag'] = array(
			'group'    => 'title_content',
			'label'    => esc_html__( 'Title Tag', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'h1'  => 'H1',
				'h2'  => 'H2',
				'h3'  => 'H3',
				'h4'  => 'H4',
				'h5'  => 'H5',
				'h6'  => 'H6',
				'div' => 'div',
				'p'   => 'p',
			),
			'default'  => 'h3',
			'inline'   => true,
			'required' => array( array( 'show_title', '!=', '' ) ),
		);

		$this->controls['title_length'] = array(
			'group'    => 'title_content',
			'label'    => esc_html__( 'Title Length', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'min'      => 2,
			'max'      => 100,
			'required' => array( array( 'show_title', '!=', '' ) ),
		);

		// Excerpt content
		$this->controls['excerpt_length'] = array(
			'group'    => 'excerpt_content',
			'label'    => esc_html__( 'Excerpt Length', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => 30,
			'min'      => 5,
			'max'      => 100,
			'required' => array( array( 'show_excerpt', '!=', '' ) ),
		);

		// Taxonomy content
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$tax_opts   = array();
		foreach ( $taxonomies as $tax ) {
			$tax_opts[ $tax->name ] = $tax->label;
		}

		$this->controls['post_taxonomy'] = array(
			'group'    => 'taxonomy_content',
			'label'    => esc_html__( 'Taxonomy', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => $tax_opts,
			'default'  => 'category',
			'required' => array( array( 'show_taxonomy', '!=', '' ) ),
		);

		$this->controls['taxonomy_limit'] = array(
			'group'    => 'taxonomy_content',
			'label'    => esc_html__( 'Limit', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => 1,
			'min'      => 1,
			'max'      => 5,
			'required' => array( array( 'show_taxonomy', '!=', '' ) ),
		);

		// Play icon
		$this->controls['play_icon'] = array(
			'group' => 'video_style',
			'label' => esc_html__( 'Play Icon', 'bricksfly-elements-for-bricks' ),
			'type'  => 'icon',
		);

		// Style: Layout
		$this->controls['columns'] = array(
			'group'   => 'layout_style',
			'label'   => esc_html__( 'Columns', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'min'     => 1,
			'max'     => 6,
			'default' => 3,
			'css'     => array(
				array(
					'property' => 'grid-template-columns',
					'selector' => '.aab--posts',
					'value'    => 'repeat(%s, 1fr)',
				),
			),
		);

		$this->controls['column_gap'] = array(
			'group'   => 'layout_style',
			'label'   => esc_html__( 'Column Gap', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'units'   => true,
			'css'     => array(
				array(
					'property' => 'column-gap',
					'selector' => '.aab--posts',
				),
			),
			'default' => '30px',
		);

		$this->controls['row_gap'] = array(
			'group'   => 'layout_style',
			'label'   => esc_html__( 'Row Gap', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'units'   => true,
			'css'     => array(
				array(
					'property' => 'row-gap',
					'selector' => '.aab--posts',
				),
			),
			'default' => '35px',
		);

		$this->controls['post_overlay_bg'] = array(
			'group' => 'layout_style',
			'label' => esc_html__( 'Overlay Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab--post::after',
				),
			),
		);

		// Style: Video
		$this->controls['thumb_width'] = array(
			'group' => 'video_style',
			'label' => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => '.thumb',
				),
			),
		);

		$this->controls['thumb_height'] = array(
			'group' => 'video_style',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => '.thumb',
				),
			),
		);

		$this->controls['duration_color'] = array(
			'group' => 'video_style',
			'label' => esc_html__( 'Duration Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.duration-wrap',
				),
				array(
					'property' => 'fill',
					'selector' => '.duration-wrap',
				),
			),
		);

		$this->controls['duration_typography'] = array(
			'group' => 'video_style',
			'label' => esc_html__( 'Duration Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => '.duration',
				),
			),
		);

		$this->controls['play_icon_size'] = array(
			'group' => 'video_style',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'font-size',
					'selector' => '.duration-wrap .icon',
				),
			),
		);

		// Style: Content
		$this->controls['content_padding'] = array(
			'group' => 'content_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.content',
				),
			),
		);

		// Style: Title
		$this->controls['title_typography'] = array(
			'group'    => 'title_style',
			'label'    => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'     => 'typography',
			'css'      => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-post-title',
				),
			),
			'required' => array( array( 'show_title', '!=', '' ) ),
		);

		$this->controls['title_color'] = array(
			'group'    => 'title_style',
			'label'    => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.aab-post-title',
				),
			),
			'required' => array( array( 'show_title', '!=', '' ) ),
		);

		$this->controls['title_margin'] = array(
			'group'    => 'title_style',
			'label'    => esc_html__( 'Margin', 'bricksfly-elements-for-bricks' ),
			'type'     => 'dimensions',
			'css'      => array(
				array(
					'property' => 'margin',
					'selector' => '.aab-post-title',
				),
			),
			'required' => array( array( 'show_title', '!=', '' ) ),
		);

		// Style: Excerpt
		$this->controls['excerpt_color'] = array(
			'group'    => 'excerpt_style',
			'label'    => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.aab-post-excerpt',
				),
			),
			'required' => array( array( 'show_excerpt', '!=', '' ) ),
		);

		$this->controls['excerpt_typography'] = array(
			'group'    => 'excerpt_style',
			'label'    => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'     => 'typography',
			'css'      => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-post-excerpt',
				),
			),
			'required' => array( array( 'show_excerpt', '!=', '' ) ),
		);

		// Style: Taxonomy
		$this->controls['taxonomy_typography'] = array(
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'     => 'typography',
			'css'      => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-post-taxonomy a',
				),
			),
			'required' => array( array( 'show_taxonomy', '!=', '' ) ),
		);

		$this->controls['taxonomy_color'] = array(
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.aab-post-taxonomy a',
				),
			),
			'required' => array( array( 'show_taxonomy', '!=', '' ) ),
		);

		$this->controls['taxonomy_bg'] = array(
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.aab-post-taxonomy a',
				),
			),
			'required' => array( array( 'show_taxonomy', '!=', '' ) ),
		);

		$this->controls['taxonomy_padding'] = array(
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'     => 'dimensions',
			'css'      => array(
				array(
					'property' => 'padding',
					'selector' => '.aab-post-taxonomy a',
				),
			),
			'required' => array( array( 'show_taxonomy', '!=', '' ) ),
		);

		$this->controls['taxonomy_border'] = array(
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'     => 'border',
			'css'      => array(
				array(
					'property' => 'border',
					'selector' => '.aab-post-taxonomy a',
				),
			),
			'required' => array( array( 'show_taxonomy', '!=', '' ) ),
		);

		// Style: Meta
		$this->controls['meta_color'] = array(
			'group'    => 'meta_style',
			'label'    => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.aab-post-meta',
				),
			),
			'required' => array( array( 'show_meta', '!=', '' ) ),
		);

		$this->controls['meta_typography'] = array(
			'group'    => 'meta_style',
			'label'    => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'     => 'typography',
			'css'      => array(
				array(
					'property' => 'typography',
					'selector' => '.aab-post-meta',
				),
			),
			'required' => array( array( 'show_meta', '!=', '' ) ),
		);

		$this->controls['meta_gap'] = array(
			'group'    => 'meta_style',
			'label'    => esc_html__( 'Gap', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => array(
				array(
					'property' => 'gap',
					'selector' => '.aab-post-meta',
				),
			),
			'required' => array( array( 'show_meta', '!=', '' ) ),
		);
	}

	public function render() {
		$s = $this->settings;

		$post_type      = $s['post_type'] ?? 'post';
		$order_by       = $s['post_order_by'] ?? 'date';
		$order          = $s['post_order'] ?? 'desc';
		$posts_per      = $s['posts_per_page'] ?? 6;
		$show_title     = ! empty( $s['show_title'] );
		$show_excerpt   = ! empty( $s['show_excerpt'] );
		$show_taxonomy  = ! empty( $s['show_taxonomy'] );
		$show_meta      = ! empty( $s['show_meta'] );
		$title_tag      = $s['title_tag'] ?? 'h3';
		$title_length   = $s['title_length'] ?? 0;
		$excerpt_length = $s['excerpt_length'] ?? 30;
		$taxonomy       = $s['post_taxonomy'] ?? 'category';
		$tax_limit      = $s['taxonomy_limit'] ?? 1;

		$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p' );
		$title_tag    = in_array( $title_tag, $allowed_tags, true ) ? $title_tag : 'h3';

		$query = new \WP_Query(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => $posts_per,
				'orderby'        => $order_by,
				'order'          => $order,
				'post_status'    => 'publish',
			)
		);

		if ( ! $query->have_posts() ) {
			echo '<p>' . esc_html__( 'No posts found.', 'bricksfly-elements-for-bricks' ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$this->set_attribute( '_root', 'class', 'aab--video-story' );

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );
		echo '<div class="aab--posts">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id = get_the_ID();

			echo '<article class="aab--post">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Thumbnail / Video
			echo '<div class="thumb">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( 'video-story' === $post_type ) {
				$video_link = get_post_meta( $post_id, '_video_story_link', true );
				if ( $video_link ) {
					echo '<video loop muted><source src="' . esc_url( $video_link ) . '" type="video/mp4"></video>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			} else {
				$thumb = get_the_post_thumbnail_url( $post_id, 'full' );
				if ( $thumb ) {
					echo '<img src="' . esc_url( $thumb ) . '" alt="' . esc_attr( get_the_title() ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Content overlay
			echo '<div class="content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			// Taxonomy
			if ( $show_taxonomy ) {
				$terms = get_the_terms( $post_id, $taxonomy );
				if ( $terms && ! is_wp_error( $terms ) ) {
					echo '<div class="aab-post-taxonomy">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$count = 0;
					foreach ( $terms as $term ) {
						if ( $tax_limit > 0 && $count >= $tax_limit ) {
							break;
						}
						echo '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						++$count;
					}
					echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}

			// Title
			if ( $show_title ) {
				$title = get_the_title();
				if ( $title_length > 0 ) {
					$title = wp_trim_words( $title, $title_length, '...' );
				}
				echo '<' . tag_escape( $title_tag ) . ' class="aab-post-title">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( $title ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</' . tag_escape( $title_tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Excerpt
			if ( $show_excerpt ) {
				echo '<div class="aab-post-excerpt">' . esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length, '...' ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Meta
			if ( $show_meta ) {
				echo '<div class="aab-post-meta">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<span>' . esc_html( get_the_date() ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			// Duration wrap
			echo '<div class="duration-wrap">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if ( ! empty( $s['play_icon'] ) ) {
				echo wp_kses_post( '<span class="icon">' . self::render_icon( $s['play_icon'] ) . '</span>' );
			} else {
				echo '<span class="icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><polygon points="5,3 19,12 5,21"/></svg></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '<span class="duration"></span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</article>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		wp_reset_postdata();
	}
}
