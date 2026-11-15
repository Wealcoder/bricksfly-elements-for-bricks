<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Video_Box_Slider extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-video-box-slider';
	public $icon         = 'ti-video-clapper aab-element-marker';
	public $css_selector = '.aab-video-box-slider';
	public $scripts      = array( 'bricksflyVideoBoxSlider' );

	public function get_label() {
		return esc_html__( 'Video Box Slider', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'video', 'slider', 'carousel', 'swiper', 'popup', 'youtube', 'vimeo' );
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
			BRICKSFLY_URL . 'public/build/elements/video-popup.css',
			array(),
			'1.0.0'
		);

		// Slider-specific CSS
		wp_enqueue_style(
			'aab-video-box-slider',
			BRICKSFLY_URL . 'public/build/elements/video-box-slider.css',
			array( 'bricks-swiper', 'aab-video-popup' ),
			'1.0.0'
		);

		// Slider + popup JS
		wp_enqueue_script(
			'aab-video-box-slider',
			BRICKSFLY_URL . 'public/build/elements/video-box-slider.js',
			array( 'bricks-swiper' ),
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['slider'] = array(
			'title' => esc_html__( 'Video Box Slider', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['button'] = array(
			'title' => esc_html__( 'Button', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['slider_options'] = array(
			'title' => esc_html__( 'Slider Options', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['thumb_style'] = array(
			'title' => esc_html__( 'Thumbnail', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['thumb_after_style'] = array(
			'title' => esc_html__( 'Thumbnail After', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['content_style'] = array(
			'title' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['button_style'] = array(
			'title' => esc_html__( 'Button', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['button_hover_style'] = array(
			'title' => esc_html__( 'Button Hover', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['nav_style'] = array(
			'title'    => esc_html__( 'Slider Navigation', 'bricksfly-elements-for-bricks' ),
			'tab'      => 'style',
			'required' => array( 'navigation', '=', true ),
		);

		$this->control_groups['pagination_style'] = array(
			'title'    => esc_html__( 'Slider Pagination', 'bricksfly-elements-for-bricks' ),
			'tab'      => 'style',
			'required' => array( 'pagination', '=', true ),
		);
	}

	public function set_controls() {

		// =====================================================
		// CONTENT TAB → Video Box Slider
		// =====================================================

		$this->controls['videoSlides'] = array(
			'tab'           => 'content',
			'group'         => 'slider',
			'label'         => esc_html__( 'Video Slides', 'bricksfly-elements-for-bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'title',
			'fields'        => array(
				'videoThumb' => array(
					'label' => esc_html__( 'Choose Image', 'bricksfly-elements-for-bricks' ),
					'type'  => 'image',
				),
				'title'      => array(
					'label'   => esc_html__( 'Title', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'Adam Smith', 'bricksfly-elements-for-bricks' ),
				),
				'subtitle'   => array(
					'label'   => esc_html__( 'Sub Title', 'bricksfly-elements-for-bricks' ),
					'type'    => 'text',
					'default' => esc_html__( 'Developer', 'bricksfly-elements-for-bricks' ),
				),
				'videoLink'  => array(
					'label'       => esc_html__( 'Video Link', 'bricksfly-elements-for-bricks' ),
					'type'        => 'text',
					'default'     => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
					'placeholder' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
					'description' => esc_html__( 'YouTube/Vimeo link, or direct video file URL (mp4 recommended).', 'bricksfly-elements-for-bricks' ),
				),
			),
			'default'       => array(
				array(
					'title'     => 'Adam Smith',
					'subtitle'  => 'Developer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				),
				array(
					'title'     => 'Jane Doe',
					'subtitle'  => 'Designer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				),
				array(
					'title'     => 'Mark Lee',
					'subtitle'  => 'Marketer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				),
				array(
					'title'     => 'Sara Hill',
					'subtitle'  => 'Founder',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				),
				array(
					'title'     => 'John Roe',
					'subtitle'  => 'Engineer',
					'videoLink' => 'https://www.youtube.com/watch?v=XHOmBV4js_E',
				),
			),
		);

		$this->controls['titleTag'] = array(
			'tab'     => 'content',
			'group'   => 'slider',
			'label'   => esc_html__( 'Title HTML Tag', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
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
			'inline'  => true,
		);

		$this->controls['imageSize'] = array(
			'tab'     => 'content',
			'group'   => 'slider',
			'label'   => esc_html__( 'Image Size', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'thumbnail' => esc_html__( 'Thumbnail', 'bricksfly-elements-for-bricks' ),
				'medium'    => esc_html__( 'Medium', 'bricksfly-elements-for-bricks' ),
				'large'     => esc_html__( 'Large', 'bricksfly-elements-for-bricks' ),
				'full'      => esc_html__( 'Full', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'full',
			'inline'  => true,
		);

		$this->controls['showThumbAfter'] = array(
			'tab'         => 'content',
			'group'       => 'slider',
			'label'       => esc_html__( 'Thumbnail After', 'bricksfly-elements-for-bricks' ),
			'description' => esc_html__( 'Enable a shape behind the thumbnail. Use the Thumbnail After style section to design it.', 'bricksfly-elements-for-bricks' ),
			'type'        => 'checkbox',
			'default'     => false,
		);

		$this->controls['thumbAfterDisplay'] = array(
			'tab'      => 'content',
			'group'    => 'slider',
			'label'    => esc_html__( 'Display', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				''             => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
				'hover-slide'  => esc_html__( 'On Hover Slide', 'bricksfly-elements-for-bricks' ),
				'active-slide' => esc_html__( 'On Active Slide', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => '',
			'inline'   => true,
			'required' => array( 'showThumbAfter', '=', true ),
		);

		$this->controls['sliderWidth'] = array(
			'tab'   => 'content',
			'group' => 'slider',
			'label' => esc_html__( 'Slider Max Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'slider',
			'units' => array(
				'px'  => array(
					'min' => 100,
					'max' => 1500,
				),
				'%'   => array(
					'min' => 0,
					'max' => 100,
				),
				'em'  => array(
					'min' => 0,
					'max' => 100,
				),
				'rem' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'max-width',
					'selector' => '.wcf__slider',
				),
			),
		);

		// =====================================================
		// CONTENT TAB → Button (popup play button)
		// =====================================================

		$this->controls['btnText'] = array(
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Text', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => esc_html__( 'Play', 'bricksfly-elements-for-bricks' ),
		);

		$this->controls['btnIcon'] = array(
			'tab'   => 'content',
			'group' => 'button',
			'label' => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
			'type'  => 'icon',
		);

		$this->controls['iconSpacing'] = array(
			'tab'   => 'content',
			'group' => 'button',
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
			'group'   => 'button',
			'label'   => esc_html__( 'Active Ripple', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['rippleColor'] = array(
			'tab'      => 'content',
			'group'    => 'button',
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
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Active Spinner', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => false,
		);

		$this->controls['spinnerImage'] = array(
			'tab'      => 'content',
			'group'    => 'button',
			'label'    => esc_html__( 'Spinner Image', 'bricksfly-elements-for-bricks' ),
			'type'     => 'image',
			'required' => array( 'activeSpinner', '!=', '' ),
		);

		$this->controls['buttonDisplay'] = array(
			'tab'     => 'content',
			'group'   => 'button',
			'label'   => esc_html__( 'Display', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				''             => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
				'hover-slide'  => esc_html__( 'On Hover Slide', 'bricksfly-elements-for-bricks' ),
				'active-slide' => esc_html__( 'On Active Slide', 'bricksfly-elements-for-bricks' ),
			),
			'default' => '',
			'inline'  => true,
		);

		// =====================================================
		// CONTENT TAB → Slider Options
		// =====================================================

		$this->controls['slidesToShow'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Slides to Show', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'1'  => '1',
				'2'  => '2',
				'3'  => '3',
				'4'  => '4',
				'5'  => '5',
				'6'  => '6',
				'7'  => '7',
				'8'  => '8',
				'9'  => '9',
				'10' => '10',
			),
			'default' => '3',
			'inline'  => true,
		);

		$this->controls['autoplay'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Autoplay', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => false,
		);

		$this->controls['autoplayDelay'] = array(
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Autoplay Delay (ms)', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => 3000,
			'required' => array( 'autoplay', '=', true ),
		);

		$this->controls['autoplayInteraction'] = array(
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Pause on Interaction', 'bricksfly-elements-for-bricks' ),
			'type'     => 'checkbox',
			'default'  => true,
			'required' => array( 'autoplay', '=', true ),
		);

		$this->controls['allowTouchMove'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Allow Touch Move', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => false,
		);

		$this->controls['loop'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Loop', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['mousewheel'] = array(
			'tab'         => 'content',
			'group'       => 'slider_options',
			'label'       => esc_html__( 'Mousewheel', 'bricksfly-elements-for-bricks' ),
			'description' => esc_html__( 'If enabled, please disable Loop for proper behavior.', 'bricksfly-elements-for-bricks' ),
			'type'        => 'checkbox',
			'default'     => false,
		);

		$this->controls['speed'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Animation Speed (ms)', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'default' => 500,
		);

		$this->controls['spaceBetween'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Space Between (px)', 'bricksfly-elements-for-bricks' ),
			'type'    => 'number',
			'default' => 20,
		);

		$this->controls['navigation'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Navigation', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['navigationPreviousIcon'] = array(
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Previous Arrow Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['navigationNextIcon'] = array(
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Next Arrow Icon', 'bricksfly-elements-for-bricks' ),
			'type'     => 'icon',
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['pagination'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Pagination', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => true,
		);

		$this->controls['paginationType'] = array(
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Pagination Type', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				'bullets'     => esc_html__( 'Bullets', 'bricksfly-elements-for-bricks' ),
				'fraction'    => esc_html__( 'Fraction', 'bricksfly-elements-for-bricks' ),
				'progressbar' => esc_html__( 'Progressbar', 'bricksfly-elements-for-bricks' ),
			),
			'default'  => 'bullets',
			'inline'   => true,
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['direction'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Direction', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'ltr' => esc_html__( 'Left', 'bricksfly-elements-for-bricks' ),
				'rtl' => esc_html__( 'Right', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'ltr',
			'inline'  => true,
		);

		$this->controls['centerSlide'] = array(
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__( 'Center Slide', 'bricksfly-elements-for-bricks' ),
			'type'    => 'checkbox',
			'default' => false,
		);

		$this->controls['slideScaleX'] = array(
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Slide Scale X', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'min'      => 0,
			'step'     => 0.1,
			'css'      => array(
				array(
					'property' => '--scale-x',
					'selector' => '.swiper-slide',
				),
			),
			'required' => array( 'centerSlide', '=', true ),
		);

		$this->controls['slideScaleY'] = array(
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__( 'Slide Scale Y', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'min'      => 0,
			'step'     => 0.1,
			'css'      => array(
				array(
					'property' => '--scale-y',
					'selector' => '.swiper-slide',
				),
			),
			'required' => array( 'centerSlide', '=', true ),
		);

		// =====================================================
		// STYLE TAB → Thumbnail
		// =====================================================

		$this->controls['imageHeight'] = array(
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => array(
				'px' => array(
					'min'  => 0,
					'max'  => 1000,
					'step' => 1,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => '.thumb img',
				),
			),
		);

		$this->controls['imagePadding'] = array(
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => '.thumb',
				),
			),
		);

		$this->controls['imageBorderRadius'] = array(
			'tab'   => 'style',
			'group' => 'thumb_style',
			'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'border-radius',
					'selector' => '.thumb img',
				),
			),
		);

		// =====================================================
		// STYLE TAB → Thumbnail After (conditional)
		// =====================================================

		$this->controls['thumbAfterWidth'] = array(
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min'  => 0,
					'max'  => 1000,
					'step' => 1,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'      => array(
				array(
					'property' => 'width',
					'selector' => '.thumb:after',
				),
			),
			'required' => array( 'showThumbAfter', '=', true ),
		);

		$this->controls['thumbAfterHeight'] = array(
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min'  => 0,
					'max'  => 1000,
					'step' => 1,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'      => array(
				array(
					'property' => 'height',
					'selector' => '.thumb:after',
				),
			),
			'required' => array( 'showThumbAfter', '=', true ),
		);

		$this->controls['thumbAfterBackground'] = array(
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.thumb:after',
				),
			),
			'required' => array( 'showThumbAfter', '=', true ),
		);

		$this->controls['thumbAfterBorder'] = array(
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'     => 'border',
			'css'      => array(
				array(
					'property' => 'border',
					'selector' => '.thumb:after',
				),
			),
			'required' => array( 'showThumbAfter', '=', true ),
		);

		$this->controls['thumbAfterBorderRadius'] = array(
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			'type'     => 'dimensions',
			'css'      => array(
				array(
					'property' => 'border-radius',
					'selector' => '.thumb:after',
				),
			),
			'required' => array( 'showThumbAfter', '=', true ),
		);

		$this->controls['thumbAfterBoxShadow'] = array(
			'tab'      => 'style',
			'group'    => 'thumb_after_style',
			'label'    => esc_html__( 'Box Shadow', 'bricksfly-elements-for-bricks' ),
			'type'     => 'box-shadow',
			'css'      => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.thumb:after',
				),
			),
			'required' => array( 'showThumbAfter', '=', true ),
		);

		// =====================================================
		// STYLE TAB → Content
		// =====================================================

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

		$this->controls['contentBackground'] = array(
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

		$this->controls['titleMargin'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Title Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'margin',
					'selector' => '.title',
				),
			),
		);

		$this->controls['subtitleColor'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Sub Title Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => '.sub-title',
				),
			),
		);

		$this->controls['subtitleTypography'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Sub Title Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'font',
					'selector' => '.sub-title',
				),
			),
		);

		$this->controls['subtitleMargin'] = array(
			'tab'   => 'style',
			'group' => 'content_style',
			'label' => esc_html__( 'Sub Title Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'margin',
					'selector' => '.sub-title',
				),
			),
		);

		// =====================================================
		// STYLE TAB → Button (Normal)
		// =====================================================

		$this->controls['btnTypography'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
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
			'tab'         => 'style',
			'group'       => 'button_style',
			'label'       => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'default'     => '60',
			'placeholder' => '60',
			'type'        => 'number',
			'units'       => array(
				'px' => array(
					'min'  => 0,
					'max'  => 500,
					'step' => 5,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'         => array(
				array(
					'property' => 'width',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnHeight'] = array(
			'tab'         => 'style',
			'group'       => 'button_style',
			'label'       => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'        => 'number',
			'default'     => '60',
			'placeholder' => '60',
			'units'       => array(
				'px' => array(
					'min'  => 0,
					'max'  => 500,
					'step' => 5,
				),
				'%'  => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'         => array(
				array(
					'property' => 'height',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnBorder'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		// $this->controls['btnBorderRadius'] = [
		// 'tab'   => 'style',
		// 'group' => 'button_style',
		// 'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
		// 'type'  => 'dimensions',
		// 'css'   => [
		// [
		// 'property' => 'border-radius',
		// 'selector' => '.aab-popup-btn',
		// ],
		// [
		// 'property' => 'border-radius',
		// 'selector' => '.aab-popup-btn::before',
		// ],
		// [
		// 'property' => 'border-radius',
		// 'selector' => '.aab-popup-btn::after',
		// ],
		// [
		// 'property' => 'border-radius',
		// 'selector' => '.aab-popup-btn .spinner-image',
		// ],
		// ],
		// ];

		$this->controls['btnBoxShadow'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Box Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => array(
				array(
					'property' => 'box-shadow',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnPadding'] = array(
			'tab'         => 'style',
			'group'       => 'button_style',
			'label'       => esc_html__( 'Spacing', 'bricksfly-elements-for-bricks' ),
			'type'        => 'dimensions',
			'default'     => '10',
			'placeholder' => '10',
			'css'         => array(
				array(
					'property' => 'padding',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		$this->controls['btnTextColor'] = array(
			'tab'   => 'style',
			'group' => 'button_style',
			'label' => esc_html__( 'Text Color', 'bricksfly-elements-for-bricks' ),
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
			'group' => 'button_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => '.aab-popup-btn',
				),
			),
		);

		// =====================================================
		// STYLE TAB → Button Hover
		// =====================================================

		$this->controls['btnHoverColor'] = array(
			'tab'   => 'style',
			'group' => 'button_hover_style',
			'label' => esc_html__( 'Text Color', 'bricksfly-elements-for-bricks' ),
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

		$this->controls['btnHoverBackground'] = array(
			'tab'   => 'style',
			'group' => 'button_hover_style',
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
			'group' => 'button_hover_style',
			'label' => esc_html__( 'Border Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'border-color',
					'selector' => '.aab-popup-btn:hover',
				),
			),
		);

		// =====================================================
		// STYLE TAB → Slider Navigation (conditional)
		// =====================================================

		$this->controls['arrowSize'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Arrow Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min' => 5,
					'max' => 100,
				),
			),
			'css'      => array(
				array(
					'property' => 'font-size',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowCircleSize'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Circle Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'css'      => array(
				array(
					'property' => 'width',
					'selector' => '.wcf-arrow',
				),
				array(
					'property' => 'height',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowBorder'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'     => 'border',
			'css'      => array(
				array(
					'property' => 'border',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowBorderRadius'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			'type'     => 'dimensions',
			'css'      => array(
				array(
					'property' => 'border-radius',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowPadding'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min' => 0,
					'max' => 200,
				),
			),
			'css'      => array(
				array(
					'property' => 'padding',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowColor'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.wcf-arrow',
				),
				array(
					'property' => 'fill',
					'selector' => '.wcf-arrow svg',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowBackground'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.wcf-arrow',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowHoverColor'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Hover Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.wcf-arrow:hover',
				),
				array(
					'property' => 'fill',
					'selector' => '.wcf-arrow:hover svg',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowHoverBackground'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Hover Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.wcf-arrow:hover',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['arrowHoverBorderColor'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Hover Border Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'border-color',
					'selector' => '.wcf-arrow:hover',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['navigationAlign'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'     => 'select',
			'options'  => array(
				''              => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
				'flex-start'    => esc_html__( 'Start', 'bricksfly-elements-for-bricks' ),
				'center'        => esc_html__( 'Center', 'bricksfly-elements-for-bricks' ),
				'flex-end'      => esc_html__( 'End', 'bricksfly-elements-for-bricks' ),
				'space-between' => esc_html__( 'Space Between', 'bricksfly-elements-for-bricks' ),
			),
			'inline'   => true,
			'css'      => array(
				array(
					'property' => 'justify-content',
					'selector' => '.ts-navigation',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		$this->controls['navigationGap'] = array(
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__( 'Gap', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'      => array(
				array(
					'property' => 'gap',
					'selector' => '.ts-navigation',
				),
			),
			'required' => array( 'navigation', '=', true ),
		);

		// =====================================================
		// STYLE TAB → Slider Pagination (conditional)
		// =====================================================

		$this->controls['bulletsInactiveColor'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet:not(.swiper-pagination-bullet-active)',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['bulletsActiveColor'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Active Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background',
					'selector' => '.swiper-pagination-bullet',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['bulletsSize'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Size', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min' => 2,
					'max' => 100,
				),
			),
			'css'      => array(
				array(
					'property' => 'width',
					'selector' => '.swiper-pagination-bullet',
				),
				array(
					'property' => 'height',
					'selector' => '.swiper-pagination-bullet',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['bulletsGap'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Bullets Gap', 'bricksfly-elements-for-bricks' ),
			'type'     => 'slider',
			'units'    => array(
				'px' => array(
					'min' => 0,
					'max' => 100,
				),
			),
			'css'      => array(
				array(
					'property' => 'gap',
					'selector' => '.swiper-pagination-bullets',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['fractionCurrentColor'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Fraction Current Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.swiper-pagination-current',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['fractionTotalColor'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Fraction Total Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'color',
					'selector' => '.swiper-pagination-total',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['fractionMidLineColor'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Fraction Mid Line Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.mid-line',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['progressColor'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Progressbar Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.swiper-pagination-progressbar',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);

		$this->controls['progressFillColor'] = array(
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__( 'Progressbar Fill Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'css'      => array(
				array(
					'property' => 'background-color',
					'selector' => '.swiper-pagination-progressbar-fill',
				),
			),
			'required' => array( 'pagination', '=', true ),
		);
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

		// Vimeo
		if ( preg_match( '/vimeo\.com/', $url ) ) {
			$video_id = '';
			if ( preg_match( '/vimeo\.com\/(\d+)/', $url, $m ) ) {
				$video_id = $m[1];
			} elseif ( preg_match( '/player\.vimeo\.com\/video\/(\d+)/', $url, $m ) ) {
				$video_id = $m[1];
			}

			if ( $video_id ) {
				return add_query_arg(
					array(
						'autoplay' => '1',
						'title'    => '0',
						'byline'   => '0',
						'portrait' => '0',
						'dnt'      => '1',
					),
					'https://player.vimeo.com/video/' . $video_id
				);
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
			echo wp_kses_post( self::render_icon( $this->settings[ $icon_key ], array( 'aria-hidden' => 'true' ) ) );
		} else {
			echo '<i class="fas fa-chevron-' . esc_attr( $direction ) . '" aria-hidden="true"></i>';
		}
	}

	public function render() {
		$settings = $this->settings;

		if ( empty( $settings['videoSlides'] ) || ! is_array( $settings['videoSlides'] ) ) {
			return $this->render_element_placeholder( array( 'title' => esc_html__( 'No video slides added.', 'bricksfly-elements-for-bricks' ) ) );
		}

		$image_size = $settings['imageSize'] ?? 'full';
		$title_tag  = $settings['titleTag'] ?? 'h4';
		$title_tag  = in_array( $title_tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'p' ), true ) ? $title_tag : 'h4';
		$direction  = $settings['direction'] ?? 'ltr';
		$show_nav   = ! empty( $settings['navigation'] );
		$show_pag   = ! empty( $settings['pagination'] );
		$pag_type   = $settings['paginationType'] ?? 'bullets';

		// Root classes (Elementor prefix_class equivalents)
		$root_classes = array( 'aab-video-box-slider', 'wcf__slider-wrapper', 'wcf__video_slider' );

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
		$slider_settings = array(
			'loop'           => ! empty( $settings['loop'] ),
			'speed'          => (int) ( $settings['speed'] ?? 500 ),
			'allowTouchMove' => ! empty( $settings['allowTouchMove'] ),
			'slidesPerView'  => (int) ( $settings['slidesToShow'] ?? 3 ),
			'spaceBetween'   => (int) ( $settings['spaceBetween'] ?? 20 ),
			'centeredSlides' => ! empty( $settings['centerSlide'] ),
		);

		if ( ! empty( $settings['autoplay'] ) ) {
			$slider_settings['autoplay'] = array(
				'delay'                => (int) ( $settings['autoplayDelay'] ?? 3000 ),
				'disableOnInteraction' => ! empty( $settings['autoplayInteraction'] ),
			);
		}

		if ( ! empty( $settings['mousewheel'] ) ) {
			$slider_settings['mousewheel'] = array( 'releaseOnEdges' => true );
		}

		if ( $show_nav ) {
			$slider_settings['navigation'] = array(
				'nextEl' => '.wcf-arrow-next',
				'prevEl' => '.wcf-arrow-prev',
			);
		}

		if ( $show_pag ) {
			$slider_settings['pagination'] = array(
				'el'        => '.swiper-pagination',
				'clickable' => true,
				'type'      => $pag_type,
			);
		}

		echo wp_kses_post( '<div ' . $this->render_attributes( '_root' ) . '>' );

		echo '<div class="wcf__slider swiper" dir="' . esc_attr( $direction ) . '" style="position: static">';
		echo '<div class="swiper-wrapper" data-settings="' . esc_attr( wp_json_encode( $slider_settings ) ) . '">';

		foreach ( $settings['videoSlides'] as $index => $item ) {
			$image_url = $this->get_image_url( $item['videoThumb'] ?? '', $image_size );
			if ( ! $image_url ) {
				$image_url = BRICKS_URL_ASSETS . 'images/placeholder-image-800x600.jpg';
			}
			$alt_text    = esc_attr( $item['title'] ?? '' );
			$video_link  = ! empty( $item['videoLink'] ) ? $this->parse_video_url( $item['videoLink'] ) : '';
			$btn_classes = array( 'aab-popup-btn', 'wcf-popup-btn' );
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
			echo '<button class="' . esc_attr( implode( ' ', $btn_classes ) ) . '" data-src="' . esc_url( $video_link ) . '" aria-label="' . esc_attr__( 'Play Video', 'bricksfly-elements-for-bricks' ) . '">';

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
				echo wp_kses_post( self::render_icon( $settings['btnIcon'], array( 'aria-hidden' => 'true' ) ) );
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
		echo '<button class="aab-popup-close" aria-label="' . esc_attr__( 'Close', 'bricksfly-elements-for-bricks' ) . '">&times;</button>';
		echo '<div class="aab-popup-content-container"></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo '</div>'; // root
	}
}
