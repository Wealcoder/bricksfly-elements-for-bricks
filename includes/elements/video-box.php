<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Video_Box extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-video-box';
	public $icon         = 'ti-video-clapper aab-element-marker';
	public $css_selector = '.aab-video-box';
	public $scripts      = array( 'bricksflyVideoBox' );

	public function get_label() {
		return esc_html__( 'Video Box', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'video', 'box', 'play', 'thumbnail', 'popup' );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		wp_enqueue_style(
			'aab-video-popup',
			BRICKSFLY_URL . 'public/build/elements/video-popup.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_style(
			'aab-video-box',
			BRICKSFLY_URL . 'public/build/elements/video-box.css',
			array(),
			'1.0.0'
		);

		// Reuse the video popup JS for open/close/GSAP
		wp_enqueue_script(
			'aab-video-popup',
			BRICKSFLY_URL . 'public/build/elements/video-popup.js',
			array(),
			'1.0.0',
			true
		);

		wp_enqueue_script(
			'aab-video-box',
			BRICKSFLY_URL . 'public/build/elements/video-box.js',
			array( 'aab-video-popup' ),
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['layout'] = array(
			'title' => esc_html__( 'Layout', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['thumbnail'] = array(
			'title' => esc_html__( 'Thumbnail', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['video_content'] = array(
			'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['play_button'] = array(
			'title' => esc_html__( 'Button', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['video_link'] = array(
			'title' => esc_html__( 'Video', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['box_style'] = array(
			'title' => esc_html__( 'Video Box', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['thumb_style'] = array(
			'title' => esc_html__( 'Thumbnail', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['content_style'] = array(
			'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['btn_style'] = array(
			'title' => esc_html__( 'Button', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['btn_hover'] = array(
			'title' => esc_html__( 'Button Hover', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
	}

	public function set_controls() {

		// --- Layout ---

		$this->controls['layoutStyle'] = array(
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'Style', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => array(
				'1' => esc_html__( 'Style One', 'bricksfly-elements-for-bricks' ),
				'2' => esc_html__( 'Style Two', 'bricksfly-elements-for-bricks' ),
			),
			'default' => '1',
		);

		$this->controls['boxAlign'] = array(
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => array(
				array(
					'property' => 'text-align',
					'selector' => '.aab-video-box',
				),
			),
		);

		// --- Thumbnail ---

		$this->controls['thumbnailType'] = array(
			'tab'     => 'content',
			'group'   => 'thumbnail',
			'label'   => esc_html__( 'Type', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => array(
				'image' => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
				'video' => esc_html__( 'Video', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'image',
		);

		$this->controls['thumbImage'] = array(
			'tab'      => 'content',
			'group'    => 'thumbnail',
			'label'    => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
			'type'     => 'image',
			'required' => array( 'thumbnailType', '=', 'image' ),
		);

		$this->controls['thumbImageSize'] = array(
			'tab'      => 'content',
			'group'    => 'thumbnail',
			'label'    => esc_html__( 'Image Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => $this->get_image_sizes(),
			'default'  => 'large',
			'required' => array( 'thumbnailType', '=', 'image' ),
		);

		$this->controls['thumbVideo'] = array(
			'tab'         => 'content',
			'group'       => 'thumbnail',
			'label'       => esc_html__( 'Video File URL', 'bricksfly-elements-for-bricks' ),
			'type'        => 'text',
			'placeholder' => 'https://example.com/video.mp4',
			'required'    => array( 'thumbnailType', '=', 'video' ),
		);

		// --- Content ---

		$this->controls['boxTitle'] = array(
			'tab'     => 'content',
			'group'   => 'video_content',
			'label'   => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => 'Adam Smith',
		);

		$this->controls['titleTag'] = array(
			'tab'     => 'content',
			'group'   => 'video_content',
			'label'   => esc_html__( 'Title HTML Tag', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'inline'  => true,
			'options' => array(
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'div'  => 'div',
				'span' => 'span',
				'p'    => 'p',
			),
			'default' => 'h4',
		);

		$this->controls['boxSubtitle'] = array(
			'tab'     => 'content',
			'group'   => 'video_content',
			'label'   => esc_html__( 'Sub Title', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => 'Developer',
		);

		// --- Button ---

		$this->controls['btnVisibility'] = array(
			'tab'     => 'content',
			'group'   => 'play_button',
			'label'   => esc_html__( 'Show Play Button', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'always' => esc_html__( 'Always', 'bricksfly-elements-for-bricks' ),
				'hover'  => esc_html__( 'On Hover', 'bricksfly-elements-for-bricks' ),
				'hide'   => esc_html__( 'Hide', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'always',
			'inline'  => true,
		);

		$this->controls['btnText'] = array(
			'tab'      => 'content',
			'group'    => 'play_button',
			'label'    => esc_html__( 'Text', 'bricksfly-elements-for-bricks' ),
			'type'     => 'text',
			'default'  => 'Play',
			'required' => array( 'btnVisibility', '!=', 'hide' ),
		);

		$this->controls['btnIcon'] = array(
			'tab'   => 'content',
			'group' => 'play_button',
			'label' => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
			'type'  => 'icon',
		);

		$this->controls['iconSpacing'] = array(
			'tab'   => 'content',
			'group' => 'play_button',
			'label' => esc_html__( 'Icon Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 50,
				),
			),
			'css'   => array(
				array(
					'property' => 'gap',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['activeRipple'] = array(
			'tab'     => 'content',
			'group'   => 'play_button',
			'label'   => esc_html__( 'Active Ripple', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['rippleColor'] = array(
			'tab'      => 'content',
			'group'    => 'play_button',
			'label'    => esc_html__( 'Ripple Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.aab-popup-btn.ripple::before',
				),
				array(
					'property' => 'color',
					'selector' => '.aab-popup-btn.ripple::after',
				),
			),
			'required' => array( 'activeRipple', '!=', '' ),
		);

		$this->controls['activeSpinner'] = array(
			'tab'   => 'content',
			'group' => 'play_button',
			'label' => esc_html__( 'Active Spinner', 'bricksfly-elements-for-bricks' ),
			'type'  => 'checkbox',
		);

		$this->controls['spinnerImage'] = array(
			'tab'      => 'content',
			'group'    => 'play_button',
			'label'    => esc_html__( 'Spinner Image', 'bricksfly-elements-for-bricks' ),
			'type'     => 'image',
			'required' => array( 'activeSpinner', '!=', '' ),
		);

		// --- Video Link ---

		$this->controls['videoLink'] = array(
			'tab'         => 'content',
			'group'       => 'video_link',
			'label'       => esc_html__( 'Video Link', 'bricksfly-elements-for-bricks' ),
			'type'        => 'text',
			'default'     => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
			'placeholder' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
			'description' => esc_html__( 'YouTube/Vimeo link, or direct video file URL.', 'bricksfly-elements-for-bricks' ),
		);

		// --- Style: Video Box ---

		$this->controls['boxBorder'] = array(
			'tab'   => 'style',
			'group' => 'box_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.aab-video-box',
				),
			),
		);

		$this->controls['boxBorderRadius'] = array(
			'tab'   => 'style',
			'group' => 'box_style',
			'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			// `dimensions` + `border-radius` is a Bricks bug: the CSS pipeline
			// emits `border-radius-top: 5px;` etc. which is invalid and gets
			// dropped by the browser. Per-corner radius is only special-cased
			// inside the `border` control type. Use `number` for a uniform
			// radius like the Elementor source's px/% size_units.
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 500,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'border-radius',
					'selector' => '.aab-video-box',
				),
			),
		);

		// --- Style: Thumbnail ---

		$this->controls['thumbHeight'] = array(
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 1000,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => '.aab-video-box img',
				),
				array(
					'property' => 'height',
					'selector' => '.aab-video-box .thumb video',
				),
			),
		);

		// --- Style: Content ---

		$this->controls['contentPadding'] = array(
			'tab'   => 'style',
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

		$this->controls['contentBg'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => '.content',
				),
			),
		);

		$this->controls['titleColor'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.title',
				),
			),
		);

		$this->controls['titleTypography'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.title',
				),
			),
		);

		$this->controls['titleSpacing'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'margin-bottom',
					'selector' => '.title',
				),
			),
		);

		$this->controls['subtitleColor'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.subtitle',
				),
			),
		);

		$this->controls['subtitleTypography'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.subtitle',
				),
			),
		);

		$this->controls['subtitleSpacing'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Subtitle Spacing', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'margin-bottom',
					'selector' => '.subtitle',
				),
			),
		);

		// --- Style: Button ---

		$this->controls['btnTypography'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnWidth'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 500,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnHeight'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 500,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnBorder'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnBorderRadius'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			// See boxBorderRadius — `dimensions` + `border-radius` emits
			// invalid CSS in Bricks. Uniform radius via number+units.
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min' => 0,
					'max' => 500,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'border-radius',
					'selector' => '.aab-popup-btn',
				),
				array(
					'property' => 'border-radius',
					'selector' => '.aab-popup-btn::before',
				),
				array(
					'property' => 'border-radius',
					'selector' => '.aab-popup-btn::after',
				),
				array(
					'property' => 'border-radius',
					'selector' => '.aab-popup-btn .spinner-image',
				),
			),
		);

		$this->controls['btnBoxShadow'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Box Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnColor'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-popup-btn',
				),
				array(
					'property' => 'fill',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnBackground'] = array(
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		// --- Style: Button Hover ---

		$this->controls['btnHoverColor'] = array(
			'tab'   => 'style',
			'group' => 'btn_hover',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.aab-popup-btn:hover',
				),
				array(
					'property' => 'fill',
					'selector' => '.aab-popup-btn:hover svg',
				),
			),
		);

		$this->controls['btnHoverBg'] = array(
			'tab'   => 'style',
			'group' => 'btn_hover',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => '.aab-popup-btn:hover',
				),
			),
		);

		$this->controls['btnHoverBorderColor'] = array(
			'tab'   => 'style',
			'group' => 'btn_hover',
			'label' => esc_html__( 'Border Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'border-color',
					'selector' => '.aab-popup-btn:hover',
				),
			),
		);
	}

	private function get_image_sizes() {
		$sizes   = get_intermediate_image_sizes();
		$options = array();
		foreach ( $sizes as $size ) {
			$options[ $size ] = ucwords( str_replace( array( '-', '_' ), ' ', $size ) );
		}
		$options['full'] = esc_html__( 'Full', 'bricksfly-elements-for-bricks' );
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
				return add_query_arg(
					array(
						'autoplay'       => '1',
						'rel'            => '0',
						'modestbranding' => '1',
						'enablejsapi'    => '1',
						'origin'         => home_url(),
					),
					'https://www.youtube.com/embed/' . $video_id
				);
			}
		}

		if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
			return add_query_arg(
				array(
					'autoplay' => '1',
					'title'    => '0',
					'byline'   => '0',
					'portrait' => '0',
					'dnt'      => '1',
				),
				'https://player.vimeo.com/video/' . $m[1]
			);
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
		if ( ! in_array( $btn_visibility, array( 'always', 'hover', 'hide' ), true ) ) {
			$btn_visibility = 'always';
		}

		$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' );
		if ( ! in_array( $title_tag, $allowed_tags, true ) ) {
			$title_tag = 'h4';
		}

		$this->set_attribute( '_root', 'class', array( 'aab-video-box', 'style-' . $style, 'btn-' . $btn_visibility ) );

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );

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
			$btn_classes = array( 'aab-popup-btn' );
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
				echo wp_kses_post( self::render_icon( $settings['btnIcon'], array( 'aria-hidden' => 'true' ) ) );
			}

			echo '</a>';
		}

		// Popup overlay (hidden, JS moves to body via video-popup.js)
		echo '<div class="aab-popup-source" hidden>';
		echo '<div class="aab-popup-video-wrapper">';
		echo '<div class="aab-popup-video">';
		echo '<button class="aab-popup-close" aria-label="' . esc_attr__( 'Close', 'bricksfly-elements-for-bricks' ) . '">&times;</button>';
		echo '<div class="aab-popup-content-container"></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}
}
