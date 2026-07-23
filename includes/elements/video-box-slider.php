<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class AAB_Bricks_Video_Box_Slider extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-video-box-slider';
	public $icon         = 'ti-video-clapper aab-element-marker';
	public $css_selector = '.aab-video-box-slider';
	public $scripts      = [ 'aabVideoBoxSlider' ];

	public function get_label() {
		return esc_html__('Video Box Slider', 'the-bricksfly');
	}

	public function get_keywords() {
		return [ 'video', 'slider', 'carousel', 'swiper', 'popup', 'youtube', 'vimeo' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );
		wp_enqueue_style( 'bricks-font-awesome-6-brands' );

		// Use Bricks built-in Swiper
		wp_enqueue_script( 'bricks-swiper' );
		wp_enqueue_style( 'bricks-swiper' );
		

		// Shared popup CSS (button styles, overlay) reused from video-popup element.
		wp_enqueue_style(
			'aab-video-popup',
			AAB_ADDONS_URL . 'public/build/elements/video-popup.css',
			[],
			'1.0.0'
		);

		// Slider-specific CSS
		wp_enqueue_style(
			'aab-video-box-slider',
			AAB_ADDONS_URL . 'public/build/elements/video-box-slider.css',
			[ 'bricks-swiper', 'aab-video-popup' ],
			'1.0.0'
		);

		// Slider + popup JS
		wp_enqueue_script(
			'aab-video-box-slider',
			AAB_ADDONS_URL . 'public/build/elements/video-box-slider.js',
			[ 'bricks-swiper' ],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['slider'] = [
			'title' => esc_html__('Video Box Slider', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['button'] = [
			'title' => esc_html__('Button', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['slider_options'] = [
			'title' => esc_html__('Slider Options', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['thumb_style'] = [
			'title' => esc_html__('Thumbnail', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['thumb_after_style'] = [
			'title' => esc_html__('Thumbnail After', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['content_style'] = [
			'title' => esc_html__('Content', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['button_style'] = [
			'title' => esc_html__('Button', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['button_hover_style'] = [
			'title' => esc_html__('Button Hover', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['nav_style'] = [
			'title' => esc_html__('Slider Navigation', 'the-bricksfly'),
			'tab'   => 'style',
			'required' => [ 'navigation', '=', true ],
		];

		$this->control_groups['pagination_style'] = [
			'title' => esc_html__('Slider Pagination', 'the-bricksfly'),
			'tab'   => 'style',
			'required' => [ 'pagination', '=', true ],
		];
	}

	public function set_controls() {

		// =====================================================
		// CONTENT TAB → Video Box Slider
		// =====================================================

		$this->controls['videoSlides'] = [
			'tab'     => 'content',
			'group'   => 'slider',
			'label'   => esc_html__( 'Video Slides', 'the-bricksfly' ),
			'type'    => 'repeater',
			'titleProperty' => 'title',
			'fields'  => [
				'videoThumb' => [
					'label' => esc_html__( 'Choose Image', 'the-bricksfly' ),
					'type'  => 'image',
				],
				'title' => [
					'label'   => esc_html__( 'Title', 'the-bricksfly' ),
					'type'    => 'text',
					'default' => esc_html__( 'Adam Smith', 'the-bricksfly' ),
				],
				'subtitle' => [
					'label'   => esc_html__( 'Sub Title', 'the-bricksfly' ),
					'type'    => 'text',
					'default' => esc_html__( 'Developer', 'the-bricksfly' ),
				],
				'videoLink' => [
					'label'       => esc_html__( 'Video Link', 'the-bricksfly' ),
					'type'        => 'text',
					'default'     => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
					'placeholder' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
					'description' => esc_html__( 'YouTube/Vimeo link, or direct video file URL (mp4 recommended).', 'the-bricksfly' ),
				],
			],
			'default' => [
				[
					'title'     => 'Adam Smith',
					'subtitle'  => 'Developer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				],
				[
					'title'     => 'Jane Doe',
					'subtitle'  => 'Designer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				],
				[
					'title'     => 'Mark Lee',
					'subtitle'  => 'Marketer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				],
				[
					'title'     => 'Sara Hill',
					'subtitle'  => 'Founder',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				],
				[
					'title'     => 'John Roe',
					'subtitle'  => 'Engineer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				],
			],
		];

		$this->controls['titleTag'] = [
			'tab'     => 'content',
			'group'   => 'slider',
			'label'   => esc_html__( 'Title HTML Tag', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'div'  => 'div',
				'span' => 'span',
				'p'    => 'p',
			],
			'default' => 'h4',
			'inline'  => true,
		];

		$this->controls['imageSize'] = [
			'tab'     => 'content',
			'group'   => 'slider',
			'label'   => esc_html__( 'Image Size', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'thumbnail' => esc_html__( 'Thumbnail', 'the-bricksfly' ),
				'medium'    => esc_html__( 'Medium', 'the-bricksfly' ),
				'large'     => esc_html__( 'Large', 'the-bricksfly' ),
				'full'      => esc_html__( 'Full', 'the-bricksfly' ),
			],
			'default' => 'full',
			'inline'  => true,
		];

		$this->controls['showThumbAfter'] = [
			'tab'         => 'content',
			'group'       => 'slider',
			'label'       => esc_html__( 'Thumbnail After', 'the-bricksfly' ),
			'description' => esc_html__( 'Enable a shape behind the thumbnail. Use the Thumbnail After style section to design it.', 'the-bricksfly' ),
			'type'        => 'checkbox',
			'default'     => false,
		];

		$this->controls['thumbAfterDisplay'] = [
			'tab'      => 'content',
			'group'    => 'slider',
			'label'    => esc_html__( 'Display', 'the-bricksfly' ),
			'type'     => 'select',
			'options'  => [
				''             => esc_html__( 'Default', 'the-bricksfly' ),
				'hover-slide'  => esc_html__( 'On Hover Slide', 'the-bricksfly' ),
				'active-slide' => esc_html__( 'On Active Slide', 'the-bricksfly' ),
			],
			'default'  => '',
			'inline'   => true,
			'required' => [ 'showThumbAfter', '=', true ],
		];

		$this->controls['sliderWidth'] = [
			'tab'   => 'content',
			'group' => 'slider',
			'label' => esc_html__( 'Slider Max Width', 'the-bricksfly' ),
			'type'  => 'slider',
			'units' => [
				'px' => [ 'min' => 100, 'max' => 1500 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
				'em' => [ 'min' => 0, 'max' => 100 ],
				'rem' => [ 'min' => 0, 'max' => 100 ],
			],
			'css'   => [
				[
					'property' => 'max-width',
					'selector' => '.wcf__slider',
				],
			],
		];

		// =====================================================
		// CONTENT TAB → Button (popup play button)
		// =====================================================

		$this->controls['btnText'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Text', 'the-bricksfly' ),
			'type'    => 'text',
			'default' => esc_html__( 'Play', 'the-bricksfly' ),
		];

		$this->controls['btnIcon'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Icon', 'the-bricksfly' ),
			'type'  => 'icon',
		];

		$this->controls['iconSpacing'] = [
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Icon Spacing', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 50 ],
			],
			'css'   => [
				[
					'property' => 'gap',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['activeRipple'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Active Ripple', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['rippleColor'] = [
			'tab'      => 'content',
			'group'    => 'button',
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
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Active Spinner', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['spinnerImage'] = [
			'tab'      => 'content',
			'group'    => 'button',
			'label'    => esc_html__( 'Spinner Image', 'the-bricksfly' ),
			'type'     => 'image',
			'required' => [ 'activeSpinner', '!=', '' ],
		];

		$this->controls['buttonDisplay'] = [
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Display', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				''             => esc_html__( 'Default', 'the-bricksfly' ),
				'hover-slide'  => esc_html__( 'On Hover Slide', 'the-bricksfly' ),
				'active-slide' => esc_html__( 'On Active Slide', 'the-bricksfly' ),
			],
			'default' => '',
			'inline'  => true,
		];

		// =====================================================
		// CONTENT TAB → Slider Options
		// =====================================================

		$this->controls['slidesToShow'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Slides to Show', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'1'  => '1', '2'  => '2', '3'  => '3', '4'  => '4', '5'  => '5',
				'6'  => '6', '7'  => '7', '8'  => '8', '9'  => '9', '10' => '10',
			],
			'default' => '3',
			'inline'  => true,
		];

		$this->controls['autoplay'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Autoplay', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['autoplayDelay'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Autoplay Delay (ms)', 'the-bricksfly' ),
			'type'     => 'number',
			'default'  => 3000,
			'required' => [ 'autoplay', '=', true ],
		];

		$this->controls['autoplayInteraction'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Pause on Interaction', 'the-bricksfly' ),
			'type'     => 'checkbox',
			'default'  => true,
			'required' => [ 'autoplay', '=', true ],
		];

		$this->controls['allowTouchMove'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Allow Touch Move', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['loop'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Loop', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['mousewheel'] = [
			'tab'         => 'content',
			'group'       => 'slider_options',
			'label'       => esc_html__( 'Mousewheel', 'the-bricksfly' ),
			'description' => esc_html__( 'If enabled, please disable Loop for proper behavior.', 'the-bricksfly' ),
			'type'        => 'checkbox',
			'default'     => false,
		];

		$this->controls['speed'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Animation Speed (ms)', 'the-bricksfly' ),
			'type'    => 'number',
			'default' => 500,
		];

		$this->controls['spaceBetween'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Space Between (px)', 'the-bricksfly' ),
			'type'    => 'number',
			'default' => 20,
		];

		$this->controls['navigation'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Navigation', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['navigationPreviousIcon'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Previous Arrow Icon', 'the-bricksfly' ),
			'type'     => 'icon',
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['navigationNextIcon'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Next Arrow Icon', 'the-bricksfly' ),
			'type'     => 'icon',
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['pagination'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Pagination', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['paginationType'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Pagination Type', 'the-bricksfly' ),
			'type'     => 'select',
			'options'  => [
				'bullets'     => esc_html__( 'Bullets', 'the-bricksfly' ),
				'fraction'    => esc_html__( 'Fraction', 'the-bricksfly' ),
				'progressbar' => esc_html__( 'Progressbar', 'the-bricksfly' ),
			],
			'default'  => 'bullets',
			'inline'   => true,
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['direction'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Direction', 'the-bricksfly' ),
			'type'    => 'select',
			'options' => [
				'ltr' => esc_html__( 'Left', 'the-bricksfly' ),
				'rtl' => esc_html__( 'Right', 'the-bricksfly' ),
			],
			'default' => 'ltr',
			'inline'  => true,
		];

		$this->controls['centerSlide'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Center Slide', 'the-bricksfly' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['slideScaleX'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Slide Scale X', 'the-bricksfly' ),
			'type'     => 'number',
			'min'      => 0,
			'step'     => 0.1,
			'css'      => [
				[
					'property' => '--scale-x',
					'selector' => '.swiper-slide',
				],
			],
			'required' => [ 'centerSlide', '=', true ],
		];

		$this->controls['slideScaleY'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Slide Scale Y', 'the-bricksfly' ),
			'type'     => 'number',
			'min'      => 0,
			'step'     => 0.1,
			'css'      => [
				[
					'property' => '--scale-y',
					'selector' => '.swiper-slide',
				],
			],
			'required' => [ 'centerSlide', '=', true ],
		];

		// =====================================================
		// STYLE TAB → Thumbnail
		// =====================================================

		$this->controls['imageHeight'] = [
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Height', 'the-bricksfly' ),
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 1000, 'step' => 1 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css'   => [
				[
					'property' => 'height',
					'selector' => '.thumb img',
				],
			],
		];

		$this->controls['imagePadding'] = [
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.thumb',
				],
			],
		];

		$this->controls['imageBorderRadius'] = [
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Border Radius', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'border-radius',
					'selector' => '.thumb img',
				],
			],
		];

		// =====================================================
		// STYLE TAB → Thumbnail After (conditional)
		// =====================================================

		$this->controls['thumbAfterWidth'] = [
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Width', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [
				'px' => [ 'min' => 0, 'max' => 1000, 'step' => 1 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css'      => [
				[
					'property' => 'width',
					'selector' => '.thumb:after',
				],
			],
			'required' => [ 'showThumbAfter', '=', true ],
		];

		$this->controls['thumbAfterHeight'] = [
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Height', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [
				'px' => [ 'min' => 0, 'max' => 1000, 'step' => 1 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css'      => [
				[
					'property' => 'height',
					'selector' => '.thumb:after',
				],
			],
			'required' => [ 'showThumbAfter', '=', true ],
		];

		$this->controls['thumbAfterBackground'] = [
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Background', 'the-bricksfly' ),
			'type'     => 'background',
			'css'      => [
				[
					'property' => 'background',
					'selector' => '.thumb:after',
				],
			],
			'required' => [ 'showThumbAfter', '=', true ],
		];

		$this->controls['thumbAfterBorder'] = [
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Border', 'the-bricksfly' ),
			'type'     => 'border',
			'css'      => [
				[
					'property' => 'border',
					'selector' => '.thumb:after',
				],
			],
			'required' => [ 'showThumbAfter', '=', true ],
		];

		$this->controls['thumbAfterBorderRadius'] = [
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Border Radius', 'the-bricksfly' ),
			'type'     => 'dimensions',
			'css'      => [
				[
					'property' => 'border-radius',
					'selector' => '.thumb:after',
				],
			],
			'required' => [ 'showThumbAfter', '=', true ],
		];

		$this->controls['thumbAfterBoxShadow'] = [
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Box Shadow', 'the-bricksfly' ),
			'type'     => 'box-shadow',
			'css'      => [
				[
					'property' => 'box-shadow',
					'selector' => '.thumb:after',
				],
			],
			'required' => [ 'showThumbAfter', '=', true ],
		];

		// =====================================================
		// STYLE TAB → Content
		// =====================================================

		$this->controls['contentPadding'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.content',
				],
			],
		];

		$this->controls['contentBackground'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.content',
				],
			],
		];

		$this->controls['titleColor'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.title',
				],
			],
		];

		$this->controls['titleTypography'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.title',
				],
			],
		];

		$this->controls['titleMargin'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Margin', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'margin',
					'selector' => '.title',
				],
			],
		];

		$this->controls['subtitleColor'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Sub Title Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.sub-title',
				],
			],
		];

		$this->controls['subtitleTypography'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Sub Title Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.sub-title',
				],
			],
		];

		$this->controls['subtitleMargin'] = [
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Sub Title Margin', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'css'   => [
				[
					'property' => 'margin',
					'selector' => '.sub-title',
				],
			],
		];

		// =====================================================
		// STYLE TAB → Button (Normal)
		// =====================================================

		$this->controls['btnTypography'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Typography', 'the-bricksfly' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['btnWidth'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Width', 'the-bricksfly' ),
			'default' => '60',
			'placeholder' => '60',
			'type'  => 'number',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 500, 'step' => 5 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css'   => [
				[
					'property' => 'width',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['btnHeight'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Height', 'the-bricksfly' ),
			'type'  => 'number',
			'default' => '60',
			'placeholder' => '60',
			'units' => [
				'px' => [ 'min' => 0, 'max' => 500, 'step' => 5 ],
				'%'  => [ 'min' => 0, 'max' => 100 ],
			],
			'css'   => [
				[
					'property' => 'height',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['btnBorder'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Border', 'the-bricksfly' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		// $this->controls['btnBorderRadius'] = [
		// 	'tab'   => 'style',
		// 	'group' => 'button_style',
		// 	'label' => esc_html__( 'Border Radius', 'the-bricksfly' ),
		// 	'type'  => 'dimensions',
		// 	'css'   => [
		// 		[
		// 			'property' => 'border-radius',
		// 			'selector' => '.aab-popup-btn',
		// 		],
		// 		[
		// 			'property' => 'border-radius',
		// 			'selector' => '.aab-popup-btn::before',
		// 		],
		// 		[
		// 			'property' => 'border-radius',
		// 			'selector' => '.aab-popup-btn::after',
		// 		],
		// 		[
		// 			'property' => 'border-radius',
		// 			'selector' => '.aab-popup-btn .spinner-image',
		// 		],
		// 	],
		// ];

		$this->controls['btnBoxShadow'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Box Shadow', 'the-bricksfly' ),
			'type'  => 'box-shadow',
			'css'   => [
				[
					'property' => 'box-shadow',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['btnPadding'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Spacing', 'the-bricksfly' ),
			'type'  => 'dimensions',
			'default' => '10',
			'placeholder' => '10',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['btnTextColor'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Text Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.aab-popup-btn',
				],
				[
					'property' => 'fill',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		$this->controls['btnBackground'] = [
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.aab-popup-btn',
				],
			],
		];

		// =====================================================
		// STYLE TAB → Button Hover
		// =====================================================

		$this->controls['btnHoverColor'] = [
			'tab'   => 'style',
			'group' => 'button_hover_style',
			'label' => esc_html__( 'Text Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.aab-popup-btn:hover',
				],
				[
					'property' => 'fill',
					'selector' => '.aab-popup-btn:hover svg',
				],
			],
		];

		$this->controls['btnHoverBackground'] = [
			'tab'   => 'style',
			'group' => 'button_hover_style',
			'label' => esc_html__( 'Background', 'the-bricksfly' ),
			'type'  => 'background',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.aab-popup-btn:hover',
				],
			],
		];

		$this->controls['btnHoverBorderColor'] = [
			'tab'   => 'style',
			'group' => 'button_hover_style',
			'label' => esc_html__( 'Border Color', 'the-bricksfly' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'border-color',
					'selector' => '.aab-popup-btn:hover',
				],
			],
		];

		// =====================================================
		// STYLE TAB → Slider Navigation (conditional)
		// =====================================================

		$this->controls['arrowSize'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Arrow Size', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [ 'px' => [ 'min' => 5, 'max' => 100 ] ],
			'css'      => [
				[
					'property' => 'font-size',
					'selector' => '.wcf-arrow',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowCircleSize'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Circle Size', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'      => [
				[
					'property' => 'width',
					'selector' => '.wcf-arrow',
				],
				[
					'property' => 'height',
					'selector' => '.wcf-arrow',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowBorder'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Border', 'the-bricksfly' ),
			'type'     => 'border',
			'css'      => [
				[
					'property' => 'border',
					'selector' => '.wcf-arrow',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowBorderRadius'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Border Radius', 'the-bricksfly' ),
			'type'     => 'dimensions',
			'css'      => [
				[
					'property' => 'border-radius',
					'selector' => '.wcf-arrow',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowPadding'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Padding', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
			'css'      => [
				[
					'property' => 'padding',
					'selector' => '.wcf-arrow',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowColor'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.wcf-arrow',
				],
				[
					'property' => 'fill',
					'selector' => '.wcf-arrow svg',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowBackground'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Background', 'the-bricksfly' ),
			'type'     => 'background',
			'css'      => [
				[
					'property' => 'background',
					'selector' => '.wcf-arrow',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowHoverColor'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Hover Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.wcf-arrow:hover',
				],
				[
					'property' => 'fill',
					'selector' => '.wcf-arrow:hover svg',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowHoverBackground'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Hover Background', 'the-bricksfly' ),
			'type'     => 'background',
			'css'      => [
				[
					'property' => 'background',
					'selector' => '.wcf-arrow:hover',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['arrowHoverBorderColor'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Hover Border Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'border-color',
					'selector' => '.wcf-arrow:hover',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['navigationAlign'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Alignment', 'the-bricksfly' ),
			'type'     => 'select',
			'options'  => [
				''              => esc_html__( 'Default', 'the-bricksfly' ),
				'flex-start'    => esc_html__( 'Start', 'the-bricksfly' ),
				'center'        => esc_html__( 'Center', 'the-bricksfly' ),
				'flex-end'      => esc_html__( 'End', 'the-bricksfly' ),
				'space-between' => esc_html__( 'Space Between', 'the-bricksfly' ),
			],
			'inline'   => true,
			'css'      => [
				[
					'property' => 'justify-content',
					'selector' => '.ts-navigation',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		$this->controls['navigationGap'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Gap', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'      => [
				[
					'property' => 'gap',
					'selector' => '.ts-navigation',
				],
			],
			'required' => [ 'navigation', '=', true ],
		];

		// =====================================================
		// STYLE TAB → Slider Pagination (conditional)
		// =====================================================

		$this->controls['bulletsInactiveColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet:not(.swiper-pagination-bullet-active)',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['bulletsActiveColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Active Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['bulletsSize'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Size', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [ 'px' => [ 'min' => 2, 'max' => 100 ] ],
			'css'      => [
				[
					'property' => 'width',
					'selector' => '.swiper-pagination-bullet',
				],
				[
					'property' => 'height',
					'selector' => '.swiper-pagination-bullet',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['bulletsGap'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Gap', 'the-bricksfly' ),
			'type'     => 'slider',
			'units'    => [ 'px' => [ 'min' => 0, 'max' => 100 ] ],
			'css'      => [
				[
					'property' => 'gap',
					'selector' => '.swiper-pagination-bullets',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['fractionCurrentColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Fraction Current Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.swiper-pagination-current',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['fractionTotalColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Fraction Total Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
					'selector' => '.swiper-pagination-total',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['fractionMidLineColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Fraction Mid Line Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'background-color',
					'selector' => '.mid-line',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['progressColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Progressbar Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'background-color',
					'selector' => '.swiper-pagination-progressbar',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];

		$this->controls['progressFillColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Progressbar Fill Color', 'the-bricksfly' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'background-color',
					'selector' => '.swiper-pagination-progressbar-fill',
				],
			],
			'required' => [ 'pagination', '=', true ],
		];
	}

	private function parse_video_url( $url ) {
		$url = trim( $url );

		// YouTube
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
					'autoplay'       => '1',
					'rel'            => '0',
					'modestbranding' => '1',
					'enablejsapi'    => '1',
					'origin'         => home_url(),
				], 'https://www.youtube.com/embed/' . $video_id );
			}
		}

		// Vimeo
		if ( preg_match( '/vimeo\.com/', $url ) ) {
			$video_id = '';
			if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
				$video_id = $m[1];
			} elseif ( preg_match( '/player\.vimeo\.com\/video\/(\d+)/', $url, $m ) ) {
				$video_id = $m[1];
			}

			if ( $video_id ) {
				return add_query_arg( [
					'autoplay' => '1',
					'title'    => '0',
					'byline'   => '0',
					'portrait' => '0',
					'dnt'      => '1',
				], 'https://player.vimeo.com/video/' . $video_id );
			}
		}

		return $url;
	}

	private function get_image_url( $image_data, $size = 'full' ) {
		if ( ! is_array( $image_data ) ) {
			return '';
		}

		if ( ! empty( $image_data['id'] ) ) {
			$src = wp_get_attachment_image_src( (int) $image_data['id'], $size );
			if ( $src ) {
				return $src[0];
			}
		}

		if ( ! empty( $image_data['url'] ) ) {
			return $image_data['url'];
		}

		return '';
	}

	private function render_swiper_button( $type ) {
		$icon_key  = $type === 'next' ? 'navigationNextIcon' : 'navigationPreviousIcon';
		$direction = $type === 'next' ? 'right' : 'left';

		if ( ! empty( $this->settings[ $icon_key ] ) ) {
			echo wp_kses_post( self::render_icon( $this->settings[ $icon_key ], [ 'aria-hidden' => 'true' ] ) );
		} else {
			echo '<i class="fas fa-chevron-' . esc_attr( $direction ) . '" aria-hidden="true"></i>';
		}
	}

	public function render() {
		$settings = $this->settings;

		if ( empty( $settings['videoSlides'] ) || ! is_array( $settings['videoSlides'] ) ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'No video slides added.', 'the-bricksfly' ) ] );
		}

		$image_size  = $settings['imageSize'] ?? 'full';
		$title_tag   = $settings['titleTag'] ?? 'h4';
		$title_tag   = in_array( $title_tag, [ 'h1','h2','h3','h4','h5','h6','div','span','p' ], true ) ? $title_tag : 'h4';
		$direction   = $settings['direction'] ?? 'ltr';
		$show_nav    = ! empty( $settings['navigation'] );
		$show_pag    = ! empty( $settings['pagination'] );
		$pag_type    = $settings['paginationType'] ?? 'bullets';

		// Root classes (Elementor prefix_class equivalents)
		$root_classes = [ 'aab-video-box-slider', 'wcf__slider-wrapper', 'wcf__video_slider' ];

		if ( ! empty( $settings['showThumbAfter'] ) ) {
			$root_classes[] = 'wcf-thumb-yes';
			if ( ! empty( $settings['thumbAfterDisplay'] ) ) {
				$root_classes[] = 'thumb-after-' . $settings['thumbAfterDisplay'];
			}
		}

		if ( ! empty( $settings['buttonDisplay'] ) ) {
			$root_classes[] = 'popup-button-' . $settings['buttonDisplay'];
		}

		$this->set_attribute( '_root', 'class', $root_classes );
		$this->set_attribute( '_root', 'data-element-id', $this->id );

		// Swiper settings
		$slider_settings = [
			'loop'           => ! empty( $settings['loop'] ),
			'speed'          => (int) ( $settings['speed'] ?? 500 ),
			'allowTouchMove' => ! empty( $settings['allowTouchMove'] ),
			'slidesPerView'  => (int) ( $settings['slidesToShow'] ?? 3 ),
			'spaceBetween'   => (int) ( $settings['spaceBetween'] ?? 20 ),
			'centeredSlides' => ! empty( $settings['centerSlide'] ),
		];

		if ( ! empty( $settings['autoplay'] ) ) {
			$slider_settings['autoplay'] = [
				'delay'                => (int) ( $settings['autoplayDelay'] ?? 3000 ),
				'disableOnInteraction' => ! empty( $settings['autoplayInteraction'] ),
			];
		}

		if ( ! empty( $settings['mousewheel'] ) ) {
			$slider_settings['mousewheel'] = [ 'releaseOnEdges' => true ];
		}

		if ( $show_nav ) {
			$slider_settings['navigation'] = [
				'nextEl' => '.wcf-arrow-next',
				'prevEl' => '.wcf-arrow-prev',
			];
		}

		if ( $show_pag ) {
			$slider_settings['pagination'] = [
				'el'        => '.swiper-pagination',
				'clickable' => true,
				'type'      => $pag_type,
			];
		}

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');

		echo '<div class="wcf__slider swiper" dir="' . esc_attr( $direction ) . '" style="position: static">';
		echo '<div class="swiper-wrapper" data-settings="' . esc_attr( wp_json_encode( $slider_settings ) ) . '">';

		foreach ( $settings['videoSlides'] as $index => $item ) {
			$image_url   = $this->get_image_url( $item['videoThumb'] ?? '', $image_size );
			if ( ! $image_url ) {
				$image_url = BRICKS_URL_ASSETS . 'images/placeholder-image-800x600.jpg';
			}
			$alt_text    = esc_attr( $item['title'] ?? '' );
			$video_link  = ! empty( $item['videoLink'] ) ? $this->parse_video_url( $item['videoLink'] ) : '';
			$btn_classes = [ 'aab-popup-btn', 'wcf-popup-btn' ];
			if ( ! empty( $settings['activeRipple'] ) ) {
				$btn_classes[] = 'ripple';
			}

			echo '<div class="swiper-slide">';

			// Thumb
			echo '<div class="thumb">';
			echo '<img class="swiper-slide-image" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $alt_text ) . '">';
			echo '</div>';

			// Content
			echo '<div class="content">';
			echo '<' . esc_attr( $title_tag ) . ' class="title">' . esc_html( $item['title'] ?? '' ) . '</' . esc_attr( $title_tag ) . '>';
			echo '<div class="sub-title">' . esc_html( $item['subtitle'] ?? '' ) . '</div>';
			echo '</div>';

			// Popup button
			echo '<button class="' . esc_attr( implode( ' ', $btn_classes ) ) . '" data-src="' . esc_url( $video_link ) . '" aria-label="' . esc_attr__( 'Play Video', 'the-bricksfly' ) . '">';

			if ( ! empty( $settings['activeSpinner'] ) ) {
				$spinner_url = $this->get_image_url( $settings['spinnerImage'] ?? '' );
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

			echo '</button>';

			echo '</div>'; // .swiper-slide
		}

		echo '</div>'; // .swiper-wrapper
		echo '</div>'; // .wcf__slider

		// Navigation
		if ( $show_nav && count( $settings['videoSlides'] ) > 1 ) {
			echo '<div class="ts-navigation">';
			echo '<div class="wcf-arrow wcf-arrow-prev" role="button" tabindex="0">';
			$this->render_swiper_button( 'previous' );
			echo '</div>';
			echo '<div class="wcf-arrow wcf-arrow-next" role="button" tabindex="0">';
			$this->render_swiper_button( 'next' );
			echo '</div>';
			echo '</div>';
		}

		// Pagination
		if ( $show_pag && count( $settings['videoSlides'] ) > 1 ) {
			echo '<div class="ts-pagination">';
			echo '<div class="swiper-pagination"></div>';
			echo '</div>';
		}

		// Popup overlay (hidden source — JS moves to body)
		echo '<div class="aab-popup-source" hidden>';
		echo '<div class="aab-popup-video-wrapper">';
		echo '<div class="aab-popup-video">';
		echo '<button class="aab-popup-close" aria-label="' . esc_attr__( 'Close', 'the-bricksfly' ) . '">&times;</button>';
		echo '<div class="aab-popup-content-container"></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '</div>'; // root
	}
}
