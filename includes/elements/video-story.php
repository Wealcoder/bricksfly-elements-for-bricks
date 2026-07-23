<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class THEBRBRE_Bricks_Video_Story extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-video-story';
	public $icon         = 'ti-video-clapper aab-element-marker';
	public $css_selector = '.aab--video-story';
	public $scripts      = [ 'aabVideoStory' ];

	public function get_label() {
		return esc_html__('Video Story', 'the-bricksfly');
	}

	public function get_keywords() {
		return [ 'video', 'story', 'posts', 'grid', 'media' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'aab-video-story',
			THEBRBRE_URL . 'public/build/elements/video-story.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-video-story',
			THEBRBRE_URL . 'public/build/elements/video-story.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['query'] = [
			'title' => esc_html__('Query', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['settings'] = [
			'title' => esc_html__('Settings', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['title_content'] = [
			'title' => esc_html__('Title', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['excerpt_content'] = [
			'title' => esc_html__('Excerpt', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['taxonomy_content'] = [
			'title' => esc_html__('Taxonomy', 'the-bricksfly'),
			'tab'   => 'content',
		];

		// Style
		$this->control_groups['layout_style'] = [
			'title' => esc_html__('Layout', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['video_style'] = [
			'title' => esc_html__('Video', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['content_style'] = [
			'title' => esc_html__('Content', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['title_style'] = [
			'title' => esc_html__('Title', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['excerpt_style'] = [
			'title' => esc_html__('Excerpt', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['taxonomy_style'] = [
			'title' => esc_html__('Taxonomy', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['meta_style'] = [
			'title' => esc_html__('Meta', 'the-bricksfly'),
			'tab'   => 'style',
		];
	}

	public function set_controls() {

		// Query
		$post_types = get_post_types( [ 'public' => true ], 'objects' );
		$pt_options = [];
		foreach ( $post_types as $pt ) {
			$pt_options[ $pt->name ] = $pt->label;
		}

		$this->controls['post_type'] = [
			'group'   => 'query',
			'label'   => esc_html__( 'Source', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => $pt_options,
			'default' => 'post',
		];

		$this->controls['post_order_by'] = [
			'group'   => 'query',
			'label'   => esc_html__( 'Order By', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'date'          => esc_html__( 'Date', 'the-bricksfly' ),
				'title'         => esc_html__( 'Title', 'the-bricksfly' ),
				'menu_order'    => esc_html__( 'Menu Order', 'the-bricksfly' ),
				'modified'      => esc_html__( 'Last Modified', 'the-bricksfly' ),
				'comment_count' => esc_html__( 'Comment Count', 'the-bricksfly' ),
				'rand'          => esc_html__( 'Random', 'the-bricksfly' ),
			],
			'default' => 'date',
		];

		$this->controls['post_order'] = [
			'group'   => 'query',
			'label'   => esc_html__( 'Order', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'asc'  => 'ASC',
				'desc' => 'DESC',
			],
			'default' => 'desc',
		];

		// Settings
		$this->controls['posts_per_page'] = [
			'group'   => 'settings',
			'label'   => esc_html__( 'Posts Per Page', 'the-bricksfly' ),
			'type'    => 'number',
			'default' => 6,
		];

		$this->controls['show_title'] = [
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Title', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['show_excerpt'] = [
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Excerpt', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['show_taxonomy'] = [
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Taxonomy', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['show_meta'] = [
			'group'   => 'settings',
			'label'   => esc_html__( 'Show Meta', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		// Title content
		$this->controls['title_tag'] = [
			'group'    => 'title_content',
			'label'    => esc_html__( 'Title Tag', 'the-bricksfly' ),
			'type'     => 'select',
			'options'  => [
				'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
				'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
				'div' => 'div', 'p' => 'p',
			],
			'default'  => 'h3',
			'inline'   => true,
			'required' => [[ 'show_title', '!=', '' ]],
		];

		$this->controls['title_length'] = [
			'group'    => 'title_content',
			'label'    => esc_html__( 'Title Length', 'the-bricksfly' ),
			'type'     => 'number',
			'min'      => 2,
			'max'      => 100,
			'required' => [[ 'show_title', '!=', '' ]],
		];

		// Excerpt content
		$this->controls['excerpt_length'] = [
			'group'    => 'excerpt_content',
			'label'    => esc_html__( 'Excerpt Length', 'the-bricksfly' ),
			'type'     => 'number',
			'default'  => 30,
			'min'      => 5,
			'max'      => 100,
			'required' => [[ 'show_excerpt', '!=', '' ]],
		];

		// Taxonomy content
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		$tax_opts   = [];
		foreach ( $taxonomies as $tax ) {
			$tax_opts[ $tax->name ] = $tax->label;
		}

		$this->controls['post_taxonomy'] = [
			'group'    => 'taxonomy_content',
			'label'    => esc_html__( 'Taxonomy', 'the-bricksfly' ),
			'type'     => 'select',
			'options'  => $tax_opts,
			'default'  => 'category',
			'required' => [[ 'show_taxonomy', '!=', '' ]],
		];

		$this->controls['taxonomy_limit'] = [
			'group'    => 'taxonomy_content',
			'label'    => esc_html__( 'Limit', 'the-bricksfly' ),
			'type'     => 'number',
			'default'  => 1,
			'min'      => 1,
			'max'      => 5,
			'required' => [[ 'show_taxonomy', '!=', '' ]],
		];

		// Play icon
		$this->controls['play_icon'] = [
			'group' => 'video_style',
			'label' => esc_html__( 'Play Icon', 'the-bricksfly' ),
			'type'  => 'icon',
		];

		// Style: Layout
		$this->controls['columns'] = [
			'group'   => 'layout_style',
			'label'   => esc_html__( 'Columns', 'the-bricksfly' ),
			'type'    => 'number',
			'min'     => 1,
			'max'     => 6,
			'default' => 3,
			'css'     => [[
				'property' => 'grid-template-columns',
				'selector' => '.aab--posts',
				'value'    => 'repeat(%s, 1fr)',
			]],
		];

		$this->controls['column_gap'] = [
			'group' => 'layout_style',
			'label' => esc_html__( 'Column Gap', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'column-gap',
				'selector' => '.aab--posts',
			]],
			'default' => '30px',
		];

		$this->controls['row_gap'] = [
			'group' => 'layout_style',
			'label' => esc_html__( 'Row Gap', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'row-gap',
				'selector' => '.aab--posts',
			]],
			'default' => '35px',
		];

		$this->controls['post_overlay_bg'] = [
			'group' => 'layout_style',
			'label' => esc_html__( 'Overlay Background', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [[
				'property' => 'background-color',
				'selector' => '.aab--post::after',
			]],
		];

		// Style: Video
		$this->controls['thumb_width'] = [
			'group' => 'video_style',
			'label' => esc_html__( 'Width', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'width',
				'selector' => '.thumb',
			]],
		];

		$this->controls['thumb_height'] = [
			'group' => 'video_style',
			'label' => esc_html__( 'Height', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'height',
				'selector' => '.thumb',
			]],
		];

		$this->controls['duration_color'] = [
			'group' => 'video_style',
			'label' => esc_html__( 'Duration Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.duration-wrap',
				],
				[
					'property' => 'fill',
					'selector' => '.duration-wrap',
				],
			],
		];

		$this->controls['duration_typography'] = [
			'group' => 'video_style',
			'label' => esc_html__( 'Duration Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.duration',
			]],
		];

		$this->controls['play_icon_size'] = [
			'group' => 'video_style',
			'label' => esc_html__( 'Icon Size', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'font-size',
				'selector' => '.duration-wrap .icon',
			]],
		];

		// Style: Content
		$this->controls['content_padding'] = [
			'group' => 'content_style',
			'label' => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'padding',
				'selector' => '.content',
			]],
		];

		// Style: Title
		$this->controls['title_typography'] = [
			'group'    => 'title_style',
			'label'    => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'     => 'typography',
			'css'      => [[
				'property' => 'typography',
				'selector' => '.aab-post-title',
			]],
			'required' => [[ 'show_title', '!=', '' ]],
		];

		$this->controls['title_color'] = [
			'group'    => 'title_style',
			'label'    => esc_html__( 'Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [[
				'property' => 'color',
				'selector' => '.aab-post-title',
			]],
			'required' => [[ 'show_title', '!=', '' ]],
		];

		$this->controls['title_margin'] = [
			'group'    => 'title_style',
			'label'    => esc_html__( 'Margin', 'the-bricksfly' ),
			'type'     => 'dimensions',
			'css'      => [[
				'property' => 'margin',
				'selector' => '.aab-post-title',
			]],
			'required' => [[ 'show_title', '!=', '' ]],
		];

		// Style: Excerpt
		$this->controls['excerpt_color'] = [
			'group'    => 'excerpt_style',
			'label'    => esc_html__( 'Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [[
				'property' => 'color',
				'selector' => '.aab-post-excerpt',
			]],
			'required' => [[ 'show_excerpt', '!=', '' ]],
		];

		$this->controls['excerpt_typography'] = [
			'group'    => 'excerpt_style',
			'label'    => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'     => 'typography',
			'css'      => [[
				'property' => 'typography',
				'selector' => '.aab-post-excerpt',
			]],
			'required' => [[ 'show_excerpt', '!=', '' ]],
		];

		// Style: Taxonomy
		$this->controls['taxonomy_typography'] = [
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'     => 'typography',
			'css'      => [[
				'property' => 'typography',
				'selector' => '.aab-post-taxonomy a',
			]],
			'required' => [[ 'show_taxonomy', '!=', '' ]],
		];

		$this->controls['taxonomy_color'] = [
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [[
				'property' => 'color',
				'selector' => '.aab-post-taxonomy a',
			]],
			'required' => [[ 'show_taxonomy', '!=', '' ]],
		];

		$this->controls['taxonomy_bg'] = [
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Background', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [[
				'property' => 'background-color',
				'selector' => '.aab-post-taxonomy a',
			]],
			'required' => [[ 'show_taxonomy', '!=', '' ]],
		];

		$this->controls['taxonomy_padding'] = [
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'     => 'dimensions',
			'css'      => [[
				'property' => 'padding',
				'selector' => '.aab-post-taxonomy a',
			]],
			'required' => [[ 'show_taxonomy', '!=', '' ]],
		];

		$this->controls['taxonomy_border'] = [
			'group'    => 'taxonomy_style',
			'label'    => esc_html__( 'Border', 'the-bricksfly' ),
			'type'     => 'border',
			'css'      => [[
				'property' => 'border',
				'selector' => '.aab-post-taxonomy a',
			]],
			'required' => [[ 'show_taxonomy', '!=', '' ]],
		];

		// Style: Meta
		$this->controls['meta_color'] = [
			'group'    => 'meta_style',
			'label'    => esc_html__( 'Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [[
				'property' => 'color',
				'selector' => '.aab-post-meta',
			]],
			'required' => [[ 'show_meta', '!=', '' ]],
		];

		$this->controls['meta_typography'] = [
			'group'    => 'meta_style',
			'label'    => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'     => 'typography',
			'css'      => [[
				'property' => 'typography',
				'selector' => '.aab-post-meta',
			]],
			'required' => [[ 'show_meta', '!=', '' ]],
		];

		$this->controls['meta_gap'] = [
			'group'    => 'meta_style',
			'label'    => esc_html__( 'Gap', 'the-bricksfly' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [[
				'property' => 'gap',
				'selector' => '.aab-post-meta',
			]],
			'required' => [[ 'show_meta', '!=', '' ]],
		];
	}

	public function render() {
		$s = $this->settings;

		$post_type     = $s['post_type'] ?? 'post';
		$order_by      = $s['post_order_by'] ?? 'date';
		$order         = $s['post_order'] ?? 'desc';
		$posts_per     = $s['posts_per_page'] ?? 6;
		$show_title    = ! empty( $s['show_title'] );
		$show_excerpt  = ! empty( $s['show_excerpt'] );
		$show_taxonomy = ! empty( $s['show_taxonomy'] );
		$show_meta     = ! empty( $s['show_meta'] );
		$title_tag     = $s['title_tag'] ?? 'h3';
		$title_length  = $s['title_length'] ?? 0;
		$excerpt_length = $s['excerpt_length'] ?? 30;
		$taxonomy      = $s['post_taxonomy'] ?? 'category';
		$tax_limit     = $s['taxonomy_limit'] ?? 1;

		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p' ];
		$title_tag    = in_array( $title_tag, $allowed_tags, true ) ? $title_tag : 'h3';

		$query = new \WP_Query( [
			'post_type'      => $post_type,
			'posts_per_page' => $posts_per,
			'orderby'        => $order_by,
			'order'          => $order,
			'post_status'    => 'publish',
		] );

		if ( ! $query->have_posts() ) {
			echo '<p>' . esc_html__( 'No posts found.', 'the-bricksfly' ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$this->set_attribute( '_root', 'class', 'aab--video-story' );

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');
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
						if ( $tax_limit > 0 && $count >= $tax_limit ) break;
						echo '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$count++;
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
				echo wp_kses_post('<span class="icon">' . self::render_icon( $s['play_icon'] ) . '</span>');
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
