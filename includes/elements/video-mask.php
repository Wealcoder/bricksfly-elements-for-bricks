<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class BRICKSFLY_Bricks_Video_Mask extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-video-mask';
	public $icon         = 'ti-video-camera aab-element-marker';
	public $css_selector = '.bricksfly-video-mask';
	public $scripts      = [ 'bricksflyVideoMask' ];

	public function get_label() {
		return esc_html__('Video Mask', 'bricksfly-elements-for-bricks');
	}

	public function get_keywords() {
		return [ 'video', 'mask', 'shape', 'play', 'reveal' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		wp_enqueue_style(
			'aab-video-mask',
			BRICKSFLY_URL . 'public/build/elements/video-mask.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-video-mask',
			BRICKSFLY_URL . 'public/build/elements/video-mask.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['button'] = [
			'title' => esc_html__('Button', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['video'] = [
			'title' => esc_html__('Video', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['mask'] = [
			'title' => esc_html__('Mask', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['btn_style'] = [
			'title' => esc_html__('Button', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];
	}

	public function set_controls() {

		// --- Button ---

		$this->controls['maskContentColor'] = [
			'tab'         => 'content',
			'group'       => 'button',
			'label'       => esc_html__( 'Other Section Text Color (on open)', 'bricksfly-elements-for-bricks' ),
			'type'        => 'color',
			'description' => esc_html__( 'Applied to parent ".bricksfly-video-mask-content" section when video is open.', 'bricksfly-elements-for-bricks' ),
		];

		$this->controls['openTitle'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => esc_html__( 'Watch Video', 'bricksfly-elements-for-bricks' ),
		];

		$this->controls['closeTitle'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Close Title', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => esc_html__( 'Close Video', 'bricksfly-elements-for-bricks' ),
		];

		$this->controls['playIcon'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Play Icon', 'bricksfly-elements-for-bricks' ),
			'type'    => 'icon',
			'default' => [
				'library' => 'fontawesome',
				'icon'    => 'fas fa-play',
			],
		];

		$this->controls['iconPosition'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Icon Position', 'bricksfly-elements-for-bricks' ),
			'type'    => 'direction',
			'css'     => [
				[
					'property' => 'flex-direction',
					'selector' => '.video--btn',
				],
			],
		];

		$this->controls['btnOffsetX'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Offset X', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 2000 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css' => [
				[
					'property' => 'left',
					'selector' => '.video--btn',
				],
			],
		];

		$this->controls['btnOffsetY'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Offset Y', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 2000 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css' => [
				[
					'property' => 'top',
					'selector' => '.video--btn',
				],
			],
		];

		// --- Video ---

		$this->controls['videoLink'] = [
			'tab'         => 'content',
			'group'       => 'video',
			'label'       => esc_html__( 'Video Link (mp4)', 'bricksfly-elements-for-bricks' ),
			'type'        => 'text',
			'placeholder' => 'https://example.com/video.mp4',
			'description' => esc_html__( 'Upload your mp4 video file URL.', 'bricksfly-elements-for-bricks' ),
		];

		$this->controls['videoAutoplay'] = [
			'tab'     => 'content',
			'group'   => 'video',
			'label'   => esc_html__( 'Autoplay', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['videoMute'] = [
			'tab'     => 'content',
			'group'   => 'video',
			'label'   => esc_html__( 'Mute', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['videoPlaysinline'] = [
			'tab'   => 'content',
			'group' => 'video',
			'label' => esc_html__( 'Plays Inline', 'bricksfly-elements-for-bricks' ),
			'type'  => 'checkbox',
		];

		$this->controls['videoLoop'] = [
			'tab'   => 'content',
			'group' => 'video',
			'label' => esc_html__( 'Loop', 'bricksfly-elements-for-bricks' ),
			'type'  => 'checkbox',
		];

		$this->controls['videoPoster'] = [
			'tab'   => 'content',
			'group' => 'video',
			'label' => esc_html__( 'Poster', 'bricksfly-elements-for-bricks' ),
			'type'  => 'image',
		];

		$this->controls['videoHeight'] = [
			'tab'   => 'content',
			'group' => 'video',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 1500 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css' => [
				[
					'property' => 'height',
					'selector' => 'video',
				],
			],
		];

		$this->controls['wrapperBorder'] = [
			'tab'   => 'content',
			'group' => 'video',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.bricksfly-video-mask',
				],
			],
		];

		// --- Mask ---

		$this->controls['maskShape'] = [
			'tab'     => 'content',
			'group'   => 'mask',
			'label'   => esc_html__( 'Shape', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => [
				'circle'   => esc_html__( 'Circle', 'bricksfly-elements-for-bricks' ),
				'flower'   => esc_html__( 'Flower', 'bricksfly-elements-for-bricks' ),
				'sketch'   => esc_html__( 'Sketch', 'bricksfly-elements-for-bricks' ),
				'triangle' => esc_html__( 'Triangle', 'bricksfly-elements-for-bricks' ),
				'blob'     => esc_html__( 'Blob', 'bricksfly-elements-for-bricks' ),
			],
			'default' => 'circle',
		];

		$this->controls['maskSize'] = [
			'tab'   => 'content',
			'group' => 'mask',
			'label' => esc_html__( 'Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 50, 'max' => 1000 ],
			],
			'css' => [
				[
					'property' => '-webkit-mask-size',
					'selector' => '.video-wrapper',
				],
			],
		];

		$this->controls['maskOffsetX'] = [
			'tab'   => 'content',
			'group' => 'mask',
			'label' => esc_html__( 'Offset X', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 2000 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css' => [
				[
					'property' => '-webkit-mask-position-x',
					'selector' => '.video-wrapper',
				],
			],
		];

		$this->controls['maskOffsetY'] = [
			'tab'   => 'content',
			'group' => 'mask',
			'label' => esc_html__( 'Offset Y', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 2000 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css' => [
				[
					'property' => '-webkit-mask-position-y',
					'selector' => '.video-wrapper',
				],
			],
		];

		// --- Style: Button ---

		$this->controls['btnGap'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [ [ 'property' => 'gap', 'selector' => '.video--btn' ] ],
		];

		$this->controls['btnTextAlign'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => [ [ 'property' => 'text-align', 'selector' => '.video--btn' ] ],
		];

		$this->controls['titleColor'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Title Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.title' ] ],
		];

		$this->controls['titleTypography'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Title Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'font', 'selector' => '.title' ] ],
		];

		$this->controls['iconColor'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.icon' ],
				[ 'property' => 'fill', 'selector' => '.icon' ],
			],
		];

		$this->controls['iconSize'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'   => [ [ 'property' => 'font-size', 'selector' => '.icon' ] ],
		];

		// Hover
		$this->controls['titleHoverColor'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Hover Title Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.video--btn:hover .title' ] ],
		];

		$this->controls['iconHoverColor'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Hover Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '.video--btn:hover .icon' ],
				[ 'property' => 'fill', 'selector' => '.video--btn:hover .icon' ],
			],
		];

		// Active (mask open)
		$this->controls['titleActiveColor'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Active Title Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '&.mask-open .title' ] ],
		];

		$this->controls['iconActiveColor'] = [
			'tab'   => 'style',
			'group' => 'btn_style',
			'label' => esc_html__( 'Active Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [
				[ 'property' => 'color', 'selector' => '&.mask-open .icon' ],
				[ 'property' => 'fill', 'selector' => '&.mask-open .icon' ],
			],
		];
	}

	public function render() {
		$settings = $this->settings;

		$mask_shape     = ! empty( $settings['maskShape'] ) ? $settings['maskShape'] : 'circle';
		$mask_image_url = BRICKSFLY_URL . 'public/images/mask-shapes/' . $mask_shape . '.svg';
		$content_color  = '';

		if ( ! empty( $settings['maskContentColor'] ) ) {
			$color = is_array( $settings['maskContentColor'] ) ? ( $settings['maskContentColor']['hex'] ?? '' ) : $settings['maskContentColor'];
			if ( $color ) {
				$content_color = $color;
			}
		}

		$this->set_attribute( '_root', 'class', [ 'bricksfly-video-mask' ] );

		if ( $content_color ) {
			$this->set_attribute( '_root', 'data-content-color', $content_color );
		}

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');

		// Button
		echo '<button class="video--btn">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="icon">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $settings['playIcon'] ) ) {
			echo wp_kses_post(self::render_icon( $settings['playIcon'], [ 'aria-hidden' => 'true' ] ));
		}
		echo '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="title open-title">' . esc_html( ! empty( $settings['openTitle'] ) ? $settings['openTitle'] : 'Watch Video' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="title close-title hidden">' . esc_html( ! empty( $settings['closeTitle'] ) ? $settings['closeTitle'] : 'Close Video' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Video wrapper with mask
		echo '<div class="video-wrapper" style="-webkit-mask-image: url(' . esc_url( $mask_image_url ) . ')">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Build video attributes
		$video_url = ! empty( $settings['videoLink'] ) ? $settings['videoLink'] : '';
		echo '<video width="100%" src="' . esc_url( $video_url ) . '"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( ! empty( $settings['videoAutoplay'] ) ) {
			echo ' autoplay'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $settings['videoMute'] ) ) {
			echo ' muted'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $settings['videoPlaysinline'] ) ) {
			echo ' playsinline'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $settings['videoLoop'] ) ) {
			echo ' loop'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$poster_url = '';
		if ( ! empty( $settings['videoPoster'] ) ) {
			$poster_data = $settings['videoPoster'];
			if ( is_array( $poster_data ) && ! empty( $poster_data['url'] ) ) {
				$poster_url = $poster_data['url'];
			}
		}
		if ( $poster_url ) {
			echo ' poster="' . esc_url( $poster_url ) . '"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '></video>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
