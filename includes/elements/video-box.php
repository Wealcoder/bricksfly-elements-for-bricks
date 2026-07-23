<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class THEBRBRE_Bricks_Video_Box extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-video-box';
	public $icon         = 'ti-video-clapper aab-element-marker';
	public $css_selector = '.aab-video-box';
	public $scripts      = [ 'thebrbreVideoBox' ];

	public function get_label() {
		return esc_html__('Video Box', 'the-bricksfly');
	}

	public function get_keywords() {
		return [ 'video', 'box', 'play', 'thumbnail', 'popup' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		wp_enqueue_style(
			'aab-video-popup',
			THEBRBRE_URL . 'public/build/elements/video-popup.css',
			[],
			'1.0.0'
		);

		wp_enqueue_style(
			'aab-video-box',
			THEBRBRE_URL . 'public/build/elements/video-box.css',
			[],
			'1.0.0'
		);

		// Reuse the video popup JS for open/close/GSAP
		wp_enqueue_script(
			'aab-video-popup',
			THEBRBRE_URL . 'public/build/elements/video-popup.js',
			[],
			'1.0.0',
			true
		);

		wp_enqueue_script(
			'aab-video-box',
			THEBRBRE_URL . 'public/build/elements/video-box.js',
			[ 'aab-video-popup' ],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['layout'] = [
			'title' => esc_html__('Layout', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['thumbnail'] = [
			'title' => esc_html__('Thumbnail', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['video_content'] = [
			'title' => esc_html__('Content', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['play_button'] = [
			'title' => esc_html__('Button', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['video_link'] = [
			'title' => esc_html__('Video', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['box_style'] = [
			'title' => esc_html__('Video Box', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['thumb_style'] = [
			'title' => esc_html__('Thumbnail', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['content_style'] = [
			'title' => esc_html__('Content', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['btn_style'] = [
			'title' => esc_html__('Button', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['btn_hover'] = [
			'title' => esc_html__('Button Hover', 'the-bricksfly'),
			'tab'   => 'style',
		];
	}

	public function set_controls() {

		// --- Layout ---

		$this->controls['layoutStyle'] = [
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'Style', 'the-bricksfly' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'1' => esc_html__( 'Style One', 'the-bricksfly' ),
				'2' => esc_html__( 'Style Two', 'the-bricksfly' ),
			],
			'default' => '1',
		];

		$this->controls['boxAlign'] = [
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'Alignment', 'the-bricksfly' ),
			'type'  => 'text-align',
			'css'   => [
				[
					'property' => 'text-align',
					'selector' => '.aab-video-box',
				],
			],
		];

		// --- Thumbnail ---

		$this->controls['thumbnailType'] = [
			'tab'     => 'content',
			'group'   => 'thumbnail',
			'label'   => esc_html__( 'Type', 'the-bricksfly' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'image' => esc_html__( 'Image', 'the-bricksfly' ),
				'video' => esc_html__( 'Video', 'the-bricksfly' ),
			],
			'default' => 'image',
		];

		$this->controls['thumbImage'] = [
			'tab'      => 'content',
			'group'    => 'thumbnail',
			'label'    => esc_html__( 'Image', 'the-bricksfly' ),
			'type'     => 'image',
			'required' => [ 'thumbnailType', '=', 'image' ],
		];

		$this->controls['thumbImageSize'] = [
			'tab'      => 'content',
			'group'    => 'thumbnail',
			'label'    => esc_html__( 'Image Size', 'the-bricksfly' ),
			'type'     => 'select',
			'options'  => $this->get_image_sizes(),
			'default'  => 'large',
			'required' => [ 'thumbnailType', '=', 'image' ],
		];

		$this->controls['thumbVideo'] = [
			'tab'         => 'content',
			'group'       => 'thumbnail',
			'label'       => esc_html__( 'Video File URL', 'the-bricksfly' ),
			'type'        => 'text',
			'placeholder' => 'https://example.com/video.mp4',
			'required'    => [ 'thumbnailType', '=', 'video' ],
		];

		// --- Content ---

		$this->controls['boxTitle'] = [
			'tab'     => 'content',
			'group'   => 'video_content',
			'label'   => esc_html__( 'Title', 'the-bricksfly' ),
			'type'    => 'text',
			'default' => 'Adam Smith',
		];

		$this->controls['titleTag'] = [
			'tab'     => 'content',
			'group'   => 'video_content',
			'label'   => esc_html__( 'Title HTML Tag', 'the-bricksfly' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
				'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
				'div' => 'div', 'span' => 'span', 'p' => 'p',
			],
			'default' => 'h4',
		];

		$this->controls['boxSubtitle'] = [
			'tab'     => 'content',
			'group'   => 'video_content',
			'label'   => esc_html__( 'Sub Title', 'the-bricksfly' ),
			'type'    => 'text',
			'default' => 'Developer',
		];

		// --- Button ---

		$this->controls['btnVisibility'] = [
			'tab'     => 'content',
			'group'   => 'play_button',
			'label'   => esc_html__( 'Show Play Button', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'always' => esc_html__( 'Always', 'the-bricksfly' ),
				'hover'  => esc_html__( 'On Hover', 'the-bricksfly' ),
				'hide'   => esc_html__( 'Hide', 'the-bricksfly' ),
			],
			'default' => 'always',
			'inline'  => true,
		];

		$this->controls['btnText'] = [
			'tab'      => 'content',
			'group'    => 'play_button',
			'label'    => esc_html__( 'Text', 'the-bricksfly' ),
			'type'     => 'text',
			'default'  => 'Play',
			'required' => [ 'btnVisibility', '!=', 'hide' ],
		];

		$this->controls['btnIcon'] = [
			'tab'   => 'content',
			'group' => 'play_button',
			'label' => esc_html__( 'Icon', 'the-bricksfly' ),
			'type'  => 'icon',
		];

		$this->controls['iconSpacing'] = [
			'tab'   => 'content',
			'group' => 'play_button',
			'label' => esc_html__( 'Icon Spacing', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'css'   => [
				[
					'property' => 'gap',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['activeRipple'] = [
			'tab'     => 'content',
			'group'   => 'play_button',
			'label'   => esc_html__( 'Active Ripple', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['rippleColor'] = [
			'tab'      => 'content',
			'group'    => 'play_button',
			'label'    => esc_html__( 'Ripple Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.aab-popup-btn.ripple::before',
				],
				[
					'property' => 'color',
					'selector' => '.aab-popup-btn.ripple::after',
				],
			],
			'required' => [ 'activeRipple', '!=', '' ],
		];

		$this->controls['activeSpinner'] = [
			'tab'   => 'content',
			'group' => 'play_button',
			'label' => esc_html__( 'Active Spinner', 'the-bricksfly' ),
			'type'  => 'checkbox',
		];

		$this->controls['spinnerImage'] = [
			'tab'      => 'content',
			'group'    => 'play_button',
			'label'    => esc_html__( 'Spinner Image', 'the-bricksfly' ),
			'type'     => 'image',
			'required' => [ 'activeSpinner', '!=', '' ],
		];

		// --- Video Link ---

		$this->controls['videoLink'] = [
			'tab'         => 'content',
			'group'       => 'video_link',
			'label'       => esc_html__( 'Video Link', 'the-bricksfly' ),
			'type'        => 'text',
			'default'     => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
			'placeholder' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
			'description' => esc_html__( 'YouTube/Vimeo link, or direct video file URL.', 'the-bricksfly' ),
		];

		// --- Style: Video Box ---

		$this->controls['boxBorder'] = [
			'tab'   => 'style',
			'group' => 'box_style',
			'label' => esc_html__( 'Border', 'the-bricksfly' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.aab-video-box' ] ],
		];

		$this->controls['boxBorderRadius'] = [
			'tab'   => 'style',
			'group' => 'box_style',
			'label' => esc_html__( 'Border Radius', 'the-bricksfly' ),
			// `dimensions` + `border-radius` is a Bricks bug: the CSS pipeline
			// emits `border-radius-top: 5px;` etc. which is invalid and gets
			// dropped by the browser. Per-corner radius is only special-cased
			// inside the `border` control type. Use `number` for a uniform
			// radius like the Elementor source's px/% size_units.
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 500 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css'   => [ [ 'property' => 'border-radius', 'selector' => '.aab-video-box' ] ],
		];

		// --- Style: Thumbnail ---

		$this->controls['thumbHeight'] = [
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Height', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 1000 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css' => [
				[ 'property' => 'height', 'selector' => '.aab-video-box img' ],
				[ 'property' => 'height', 'selector' => '.aab-video-box .thumb video' ],
			],
		];

		// --- Style: Content ---

		$this->controls['contentPadding'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.content' ] ],
		];

		$this->controls['contentBg'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.content' ] ],
		];

		$this->controls['titleColor'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.title' ] ],
		];

		$this->controls['titleTypography'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.title' ] ],
		];

		$this->controls['titleSpacing'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Spacing', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'margin-bottom', 'selector' => '.title' ] ],
		];

		$this->controls['subtitleColor'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.subtitle' ] ],
		];

		$this->controls['subtitleTypography'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.subtitle' ] ],
		];

		$this->controls['subtitleSpacing'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle Spacing', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'margin-bottom', 'selector' => '.subtitle' ] ],
		];

		// --- Style: Button ---

		$this->controls['btnTypography'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.aab-popup-btn' ] ],
		];

		$this->controls['btnWidth'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Width', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 500 ], '%' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'width', 'selector' => '.aab-popup-btn' ] ],
		];

		$this->controls['btnHeight'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Height', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 500 ], '%' => [ 'min' => 0, 'max' => 100 ] ],
			'css'   => [ [ 'property' => 'height', 'selector' => '.aab-popup-btn' ] ],
		];

		$this->controls['btnBorder'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Border', 'the-bricksfly' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.aab-popup-btn' ] ],
		];

		$this->controls['btnBorderRadius'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Border Radius', 'the-bricksfly' ),
			// See boxBorderRadius — `dimensions` + `border-radius` emits
			// invalid CSS in Bricks. Uniform radius via number+units.
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 500 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css'   => [
				[ 'property' => 'border-radius', 'selector' => '.aab-popup-btn' ],
				[ 'property' => 'border-radius', 'selector' => '.aab-popup-btn::before' ],
				[ 'property' => 'border-radius', 'selector' => '.aab-popup-btn::after' ],
				[ 'property' => 'border-radius', 'selector' => '.aab-popup-btn .spinner-image' ],
			],
		];

		$this->controls['btnBoxShadow'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Box Shadow', 'the-bricksfly' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.aab-popup-btn' ] ],
		];

		$this->controls['btnColor'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.aab-popup-btn' ],
				[ 'property' => 'fill', 'selector' => '.aab-popup-btn' ],
			],
		];

		$this->controls['btnBackground'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.aab-popup-btn' ] ],
		];

		// --- Style: Button Hover ---

		$this->controls['btnHoverColor'] = [
			'tab'   => 'style',
			'group' => 'btn_hover',
			'label' => esc_html__( 'Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.aab-popup-btn:hover' ],
				[ 'property' => 'fill', 'selector' => '.aab-popup-btn:hover svg' ],
			],
		];

		$this->controls['btnHoverBg'] = [
			'tab'   => 'style',
			'group' => 'btn_hover',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.aab-popup-btn:hover' ] ],
		];

		$this->controls['btnHoverBorderColor'] = [
			'tab'   => 'style',
			'group' => 'btn_hover',
			'label' => esc_html__( 'Border Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'border-color', 'selector' => '.aab-popup-btn:hover' ] ],
		];
	}

	private function get_image_sizes() {
		$sizes   = get_intermediate_image_sizes();
		$options = [];
		foreach ( $sizes as $size ) {
			$options[ $size ] = ucwords( str_replace( [ '-', '_' ], ' ', $size ) );
		}
		$options['full'] = esc_html__( 'Full', 'the-bricksfly' );
		return $options;
	}

	private function parse_video_url( $url ) {
		$url = trim( $url );

		if ( preg_match( '/(?:youtube\.com|youtu\.be)/', $url ) ) {
			$video_id = '';
			if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $url, $m ) ) {
				$video_id = $m[1];
			} elseif ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $url, $m ) ) {
				$video_id = $m[1];
			} elseif ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $url, $m ) ) {
				$video_id = $m[1];
			}
			if ( $video_id ) {
				return add_query_arg( [
					'autoplay' => '1', 'rel' => '0', 'modestbranding' => '1',
					'enablejsapi' => '1', 'origin' => home_url(),
				], 'https://www.youtube.com/embed/' . $video_id );
			}
		}

		if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
			return add_query_arg( [
				'autoplay' => '1', 'title' => '0', 'byline' => '0', 'portrait' => '0', 'dnt' => '1',
			], 'https://player.vimeo.com/video/' . $m[1] );
		}

		return $url;
	}

	public function render() {
		$settings   = $this->settings;
		$style      = ! empty( $settings['layoutStyle'] ) ? $settings['layoutStyle'] : '1';
		$thumb_type = ! empty( $settings['thumbnailType'] ) ? $settings['thumbnailType'] : 'image';
		$video_link = ! empty( $settings['videoLink'] ) ? $this->parse_video_url( $settings['videoLink'] ) : '';
		$title_tag  = ! empty( $settings['titleTag'] ) ? $settings['titleTag'] : 'h4';

		// Drives which `.btn-*` modifier class the CSS uses to position /
		// fade the play button (always visible, hover-reveal, or hidden).
		$btn_visibility = ! empty( $settings['btnVisibility'] ) ? $settings['btnVisibility'] : 'always';
		if ( ! in_array( $btn_visibility, [ 'always', 'hover', 'hide' ], true ) ) {
			$btn_visibility = 'always';
		}

		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ];
		if ( ! in_array( $title_tag, $allowed_tags, true ) ) {
			$title_tag = 'h4';
		}

		$this->set_attribute( '_root', 'class', [ 'aab-video-box', 'style-' . $style, 'btn-' . $btn_visibility ] );

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');

		// Thumbnail
		echo '<div class="thumb">';
		if ( 'video' === $thumb_type && ! empty( $settings['thumbVideo'] ) ) {
			echo '<video src="' . esc_url( $settings['thumbVideo'] ) . '" muted></video>';
		} else {
			$image_size = ! empty( $settings['thumbImageSize'] ) ? $settings['thumbImageSize'] : 'large';
			$img_data   = isset( $settings['thumbImage'] ) ? $settings['thumbImage'] : '';

			if ( is_array( $img_data ) && ! empty( $img_data['id'] ) ) {
				echo wp_get_attachment_image( $img_data['id'], $image_size );
			} elseif ( is_array( $img_data ) && ! empty( $img_data['url'] ) ) {
				echo '<img src="' . esc_url( $img_data['url'] ) . '" alt="">';
			} elseif ( is_numeric( $img_data ) && $img_data > 0 ) {
				echo wp_get_attachment_image( (int) $img_data, $image_size );
			} else {
				echo '<img src="' . esc_url( BRICKS_URL_ASSETS . 'images/placeholder-image-800x600.jpg' ) . '" alt="">';
			}
		}
		echo '</div>';

		// Content overlay
		echo '<div class="content">';
		if ( ! empty( $settings['boxTitle'] ) ) {
			echo '<' . esc_attr( $title_tag ) . ' class="title">' . esc_html( $settings['boxTitle'] ) . '</' . esc_attr( $title_tag ) . '>';
		}
		if ( ! empty( $settings['boxSubtitle'] ) ) {
			echo '<div class="subtitle">' . esc_html( $settings['boxSubtitle'] ) . '</div>';
		}
		echo '</div>';

		// Play button — skip entirely when set to hide so the markup is
		// gone (CSS .btn-hide is a belt-and-suspenders guard for cached HTML).
		if ( 'hide' !== $btn_visibility ) {
			$btn_classes = [ 'aab-popup-btn' ];
			if ( ! empty( $settings['activeRipple'] ) ) {
				$btn_classes[] = 'ripple';
			}

			echo '<a class="' . esc_attr( implode( ' ', $btn_classes ) ) . '" data-src="' . esc_url( $video_link ) . '">';

			if ( ! empty( $settings['activeSpinner'] ) && ! empty( $settings['spinnerImage'] ) ) {
				$spinner_url = is_array( $settings['spinnerImage'] ) && ! empty( $settings['spinnerImage']['url'] )
					? $settings['spinnerImage']['url'] : '';
				if ( $spinner_url ) {
					echo '<img class="spinner-image" src="' . esc_url( $spinner_url ) . '" alt="">';
				}
			}

			if ( ! empty( $settings['btnText'] ) ) {
				echo esc_html( $settings['btnText'] );
			}
			if ( ! empty( $settings['btnIcon'] ) ) {
				echo wp_kses_post( self::render_icon( $settings['btnIcon'], [ 'aria-hidden' => 'true' ] ) );
			}

			echo '</a>';
		}

		// Popup overlay (hidden, JS moves to body via video-popup.js)
		echo '<div class="aab-popup-source" hidden>';
		echo '<div class="aab-popup-video-wrapper">';
		echo '<div class="aab-popup-video">';
		echo '<button class="aab-popup-close" aria-label="' . esc_attr__( 'Close', 'the-bricksfly' ) . '">&times;</button>';
		echo '<div class="aab-popup-content-container"></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}
}
