<?php

if (! defined('ABSPATH')) exit;

class AAB_Bricks_Brand_Slider extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-brand-slider';
	public $icon         = 'ti-layout-slider aab-element-marker';
	public $css_selector = '.aab-brand-slider-wrapper';
	public $scripts      = ['aabBrandSlider'];

	public function get_label()
	{
		return esc_html__('Brand Slider', 'the-bricksfly');
	}

	public function get_keywords()
	{
		return ['brand', 'slider', 'logo', 'carousel', 'marquee'];
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style("bricks-font-awesome-6");
		wp_enqueue_style("bricks-font-awesome-6-brands");
		wp_enqueue_style('bricks-swiper');
		wp_enqueue_script('bricks-swiper');

		wp_enqueue_style(
			'aab-brand-slider',
			AAB_ADDONS_URL . 'public/build/elements/brand-slider.css',
			['bricks-swiper'],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-brand-slider',
			AAB_ADDONS_URL . 'public/build/elements/brand-slider.js',
			['bricks-swiper'],
			'1.0.0',
			true
		);
	}

	public function set_control_groups()
	{
		$this->control_groups['content'] = [
			'title' => esc_html__('Content', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['slider_options'] = [
			'title' => esc_html__('Slider Options', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['items_style'] = [
			'title'    => esc_html__('Items', 'the-bricksfly'),
			'tab'      => 'content',
		];

		$this->control_groups['image_style'] = [
			'title'    => esc_html__('Image', 'the-bricksfly'),
			'tab'      => 'content',
			'required' => ['slideContent', '=', 'image'],
		];

		$this->control_groups['text_style'] = [
			'title'    => esc_html__('Text', 'the-bricksfly'),
			'tab'      => 'content',
			'required' => ['slideContent', '=', 'text'],
		];

		// Bricks' `required` evaluator supports only single-key conditions (AND
		// across multiple, no `relation => or`), so navigation and pagination get
		// their own groups, each gated on a single toggle.
		$this->control_groups['nav_style'] = [
			'title'    => esc_html__('Navigation', 'the-bricksfly'),
			'tab'      => 'content',
			'required' => ['showNavigation', '=', 'on'],
		];

		$this->control_groups['pagination_style'] = [
			'title'    => esc_html__('Pagination', 'the-bricksfly'),
			'tab'      => 'content',
			'required' => ['showPagination', '=', 'on'],
		];
	}

	public function set_controls()
	{
		// --- Content ---

		$this->controls['slideContent'] = [
			'tab'     => 'content',
			'group'   => 'content',
			'label'   => esc_html__('Slide Content', 'the-bricksfly'),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'text'  => esc_html__('Text', 'the-bricksfly'),
				'image' => esc_html__('Image', 'the-bricksfly'),
			],
			'default' => 'text',
		];

		// Image Repeater (Updated from image-gallery to match text list layout structure)
		$this->controls['brandImages'] = [
			'tab'           => 'content',
			'group'         => 'content',
			'label'         => esc_html__('Image List', 'the-bricksfly'),
			'type'          => 'repeater',
			'titleProperty' => 'imageTitle',
			'fields'        => [
				'imageTitle' => [
					'label' => esc_html__('Image Title', 'the-bricksfly'),
					'type'  => 'text',
				],

				'image' => [
					'label' => esc_html__('Choose Image', 'the-bricksfly'),
					'type'  => 'image',
				],
			],
			'default'  => [],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['imageSize'] = [
			'tab'      => 'content',
			'group'    => 'content',
			'label'    => esc_html__('Image Size', 'the-bricksfly'),
			'type'     => 'select',
			'options'  => $this->get_image_sizes(),
			'default'  => 'medium',
			'required' => ['slideContent', '=', 'image'],
		];

		// Text repeater
		$this->controls['textSlides'] = [
			'tab'           => 'content',
			'group'         => 'content',
			'label'         => esc_html__('Text List', 'the-bricksfly'),
			'type'          => 'repeater',
			'titleProperty' => 'text',
			'fields'        => [
				'text' => [
					'label'   => esc_html__('Text', 'the-bricksfly'),
					'type'    => 'text',
					'default' => 'Designer',
				],
			],
			'default' => [
				['text' => 'Content'],
				['text' => '(Health Advisor & Coach)'],
				['text' => 'News'],
				['text' => 'Creative Director'],
			],
			'required' => ['slideContent', '=', 'text'],
		];

		$this->controls['separatorIcon'] = [
			'tab'      => 'content',
			'group'    => 'content',
			'label'    => esc_html__('Text Separator Icon', 'the-bricksfly'),
			'type'     => 'icon',
			'default'  => [
				'library' => 'fontawesome',
				'icon'    => 'far fa-star',
			],
			'required' => ['slideContent', '=', 'text'],
		];

		// --- Slider Options ---

		$this->controls['slidesPerView'] = [
			'tab'         => 'content',
			'group'       => 'slider_options',
			'label'       => esc_html__('Slides Per View', 'the-bricksfly'),
			'type'        => 'text',
			'default'     => 'auto',
			'description' => esc_html__('Number or "auto". Click the device icon to set per breakpoint.', 'the-bricksfly'),
			'breakpoints' => true,
		];

		$this->controls['speed'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__('Speed (ms)', 'the-bricksfly'),
			'type'    => 'number',
			'default' => 5000,
			'breakpoints' => true,
		];

		// Tri-state options for responsive on/off toggles. Bricks cannot distinguish an
		// unchecked checkbox from "not set", so a checkbox can't be forced OFF at a smaller
		// breakpoint while ON at desktop. A select with an explicit Default/On/Off solves it:
		// '' (Default) = inherit, 'on' = force on, 'off' = force off.
		$toggle_options = [
			''    => esc_html__('Default', 'the-bricksfly'),
			'on'  => esc_html__('On', 'the-bricksfly'),
			'off' => esc_html__('Off', 'the-bricksfly'),
		];

		$this->controls['autoplay'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__('Autoplay', 'the-bricksfly'),
			'type'    => 'select',
			'inline'  => true,
			'options' => $toggle_options,
			'default' => 'off',
			'breakpoints' => true,
		];

		$this->controls['autoplayDelay'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__('Autoplay Delay (ms)', 'the-bricksfly'),
			'type'     => 'number',
			'default'  => 0, // 0 is best for true continuous linear sliders
			'description' => esc_html__('Set to 0 or 1 for a continuous marquee effect.', 'the-bricksfly'),
			'required' => ['autoplay', '!=', 'off'],
			'breakpoints' => true,
		];

		$this->controls['loop'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__('Loop', 'the-bricksfly'),
			'type'    => 'select',
			'inline'  => true,
			'options' => $toggle_options,
			'default' => 'off',
			'breakpoints' => true,
		];

		$this->controls['pauseOnHover'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__('Pause on Hover', 'the-bricksfly'),
			'type'     => 'select',
			'inline'   => true,
			'options'  => $toggle_options,
			'default'  => '',
			'breakpoints' => true,
			'required' => ['autoplay', '!=', 'off'],
		];

		$this->controls['showNavigation'] = [
			'tab'   => 'content',
			'group' => 'slider_options',
			'label' => esc_html__('Show Navigation', 'the-bricksfly'),
			'type'  => 'select',
			'inline'  => true,
			'options' => $toggle_options,
			'default' => '',
			'breakpoints' => true,
		];

		$this->controls['showPagination'] = [
			'tab'   => 'content',
			'group' => 'slider_options',
			'label' => esc_html__('Show Pagination', 'the-bricksfly'),
			'type'  => 'select',
			'inline'  => true,
			'options' => $toggle_options,
			'default' => '',
			'breakpoints' => true,
		];

		$this->controls['reverseDirection'] = [
			'tab'   => 'content',
			'group' => 'slider_options',
			'label' => esc_html__('Reverse Direction', 'the-bricksfly'),
			'type'  => 'checkbox',
			'breakpoints' => true,
		];

		// Items Style

		$this->controls['items_alignment'] = [
			'tab'      => 'style',
			'group'    => 'items_style',
			'label'    => esc_html__('Items Alignment', 'the-bricksfly'),
			'type'     => 'select',
			'inline'   => true,
			'options'  => [
				'left'   => esc_html__('Left', 'the-bricksfly'),
				'center' => esc_html__('Center', 'the-bricksfly'),
				'right'  => esc_html__('Right', 'the-bricksfly'),
			],
			'default'  => 'left',
			'breakpoints' => true,
			'css'      => [
				['property' => 'justify-content', 'selector' => '.swiper-slide'],
			],
			
		];

		// $this->controls['spaceBetween'] = [
		// 	'tab'         => 'style',
		// 	'group'       => 'items_style',
		// 	'label'       => esc_html__('Space Between Item(px)', 'the-bricksfly'),
		// 	'type'        => 'number',
		// 	'default'     => 0,
		// 	'breakpoints' => true,
		// ];

		$this->controls['spaceBetweenItems'] = [
			'tab'         => 'style',
			'group'       => 'items_style',
			'label'       => esc_html__('Space Between Item(px)', 'the-bricksfly'),
			'type'        => 'number',
			'units'       => true,        // outputs the unit with the value
			'unit'        => 'px',        // default unit
			'default'     => '0px',
			'breakpoints' => true,
			
			'css'      => [
				['property' => 'gap', 'selector' => '.swiper-wrapper'],
			],
		];

		$this->controls['icon_position'] = [
			'tab'      => 'style',
			'group'    => 'items_style',
			'label'    => esc_html__('Icon Position', 'the-bricksfly'),
			'type'     => 'select',
			'inline'   => true,
			'options'  => [
				'row-reverse'   => esc_html__('Left', 'the-bricksfly'),
				'row' => esc_html__('Right', 'the-bricksfly'),
			],
			'default'  => 'row',
			'breakpoints' => true,
			'css'      => [
				['property' => 'flex-direction', 'selector' => '.text-slide-content'],
			],
			
		];
       
		$this->controls['spaceBetweenIconText'] = [
			'tab'         => 'style',
			'group'       => 'items_style',
			'label'       => esc_html__('Space Between Icon and Text (px)', 'the-bricksfly'),
			'type'        => 'number',
			'units'       => true,        // outputs the unit with the value
			'unit'        => 'px',        // default unit
			'default'     => '30px',
			'breakpoints' => true,
			
			'css'      => [
				['property' => 'gap', 'selector' => '.text-slide-content'],
			],
		];

		
		// --- Style: Image ---

		$this->controls['imgWidth'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Width', 'the-bricksfly'),
			'type'     => 'number',
			'units'    => [
				'px' => ['min' => 0, 'max' => 1000],
				'%'  => ['min' => 0, 'max' => 100],
			],
			'css'      => [['property' => 'width', 'selector' => '.swiper-slide img']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['imgHeight'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Height', 'the-bricksfly'),
			'type'     => 'number',
			'units'    => [
				'px' => ['min' => 0, 'max' => 1000],
				'%'  => ['min' => 0, 'max' => 100],
			],
			'css'      => [['property' => 'height', 'selector' => '.swiper-slide img']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['imgObjectFit'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Object Fit', 'the-bricksfly'),
			'type'     => 'select',
			'options'  => [
				''           => esc_html__('Default', 'the-bricksfly'),
				'fill'       => esc_html__('Fill', 'the-bricksfly'),
				'contain'    => esc_html__('Contain', 'the-bricksfly'),
				'cover'      => esc_html__('Cover', 'the-bricksfly'),
				'none'       => esc_html__('None', 'the-bricksfly'),
				'scale-down' => esc_html__('Scale Down', 'the-bricksfly'),
			],
			'css'      => [['property' => 'object-fit', 'selector' => '.swiper-slide img']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemBgColor'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Background Color', 'the-bricksfly'),
			'type'     => 'color',
			'css'      => [['property' => 'background-color', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemBorder'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Border', 'the-bricksfly'),
			'type'     => 'border',
			'css'      => [['property' => 'border', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemBorderRadius'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Border Radius', 'the-bricksfly'),
			'type'     => 'dimensions',
			'css'      => [['property' => 'border-radius', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemPadding'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Padding', 'the-bricksfly'),
			'type'     => 'dimensions',
			'css'      => [['property' => 'padding', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		// --- Style: Text ---

		$this->controls['textColor'] = [
			'tab'      => 'style',
			'group'    => 'text_style',
			'label'    => esc_html__('Text Color', 'the-bricksfly'),
			'type'     => 'color',
			'css'      => [['property' => 'color', 'selector' => '.title']],
			'required' => ['slideContent', '=', 'text'],
		];

		$this->controls['textTypography'] = [
			'tab'      => 'style',
			'group'    => 'text_style',
			'label'    => esc_html__('Typography', 'the-bricksfly'),
			'type'     => 'typography',
			'css'      => [['property' => 'font', 'selector' => '.title']],
			'required' => ['slideContent', '=', 'text'],
		];

		$this->controls['separatorColor'] = [
			'tab'      => 'style',
			'group'    => 'text_style',
			'label'    => esc_html__('Separator Color', 'the-bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'color', 'selector' => '.aab-separator-icon i'],
				['property' => 'fill', 'selector' => '.aab-separator-icon svg'],
			],
			'required' => ['slideContent', '=', 'text'],
		];

		$this->controls['separatorSize'] = [
			'tab'      => 'style',
			'group'    => 'text_style',
			'label'    => esc_html__('Separator Size', 'the-bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 6, 'max' => 300]],
			'css'      => [['property' => 'font-size', 'selector' => '.aab-separator-icon']],
			'required' => ['slideContent', '=', 'text'],
		];

		// --- Style: Navigation ---

		$this->controls['navColor'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Color', 'the-bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'color', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '=', 'on'],
		];

		$this->controls['navBg'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Background', 'the-bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'background-color', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '=', 'on'],
		];

		$this->controls['navSize'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Size', 'the-bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 10, 'max' => 100]],
			'css'      => [
				['property' => 'width', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
				['property' => 'height', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '=', 'on'],
		];

		$this->controls['navBorder'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Border', 'the-bricksfly'),
			'type'     => 'border',
			'css'      => [
				['property' => 'border', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '=', 'on'],
		];

		// --- Arrow Position ---

		$this->controls['arrowPositionSeparator'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Position', 'the-bricksfly'),
			'type'     => 'separator',
			'required' => ['showNavigation', '=', 'on'],
		];

		$this->controls['arrowHOffset'] = [
			'tab'         => 'style',
			'group'       => 'nav_style',
			'label'       => esc_html__('Horizontal Offset', 'the-bricksfly'),
			'type'        => 'number',
			'units'       => [
				'px' => ['min' => -200, 'max' => 200],
				'%'  => ['min' => -50, 'max' => 50],
			],
			'description' => esc_html__('Positive: inside the slider. Negative: outside.', 'the-bricksfly'),
			'css'         => [
				['property' => 'left',  'selector' => '.aab-arrow-prev'],
				['property' => 'right', 'selector' => '.aab-arrow-next'],
			],
			'required'    => ['showNavigation', '=', 'on'],
		];

		$this->controls['arrowVOffset'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Vertical Position', 'the-bricksfly'),
			'type'     => 'number',
			'units'    => [
				'%'  => ['min' => 0,    'max' => 100],
				'px' => ['min' => -200, 'max' => 1000],
			],
			'css'      => [
				['property' => 'top', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '=', 'on'],
		];

		$this->controls['paginationColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__('Pagination Color', 'the-bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'background-color', 'selector' => '.swiper-pagination-bullet'],
			],
			'required' => ['showPagination', '=', 'on'],
		];

		$this->controls['paginationActiveColor'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__('Pagination Active Color', 'the-bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'background-color', 'selector' => '.swiper-pagination-bullet-active'],
			],
			'required' => ['showPagination', '=', 'on'],
		];

		$this->controls['paginationSize'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__('Pagination Dot Size', 'the-bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 2, 'max' => 60]],
			'css'      => [
				['property' => 'width',  'selector' => '.swiper-pagination-bullet'],
				['property' => 'height', 'selector' => '.swiper-pagination-bullet'],
			],
			'required' => ['showPagination', '=', 'on'],
		];

		$this->controls['paginationActiveSize'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__('Active Dot Size', 'the-bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 2, 'max' => 80]],
			'css'      => [
				['property' => 'width',  'selector' => '.swiper-pagination-bullet-active'],
				['property' => 'height', 'selector' => '.swiper-pagination-bullet-active'],
			],
			'required' => ['showPagination', '=', 'on'],
		];

		$this->controls['paginationAlign'] = [
			'tab'      => 'style',
			'group'    => 'pagination_style',
			'label'    => esc_html__('Pagination Alignment', 'the-bricksfly'),
			'type'     => 'select',
			'inline'   => true,
			'options'  => [
				'left'   => esc_html__('Left', 'the-bricksfly'),
				'center' => esc_html__('Center', 'the-bricksfly'),
				'right'  => esc_html__('Right', 'the-bricksfly'),
			],
			'default'  => 'center',
			'css'      => [
				['property' => 'justify-content', 'selector' => '.swiper-pagination-bullets'],
			],
			'required' => ['showPagination', '=', 'on'],
		];
	}

	private function get_image_sizes()
	{
		$sizes   = get_intermediate_image_sizes();
		$options = [];
		foreach ($sizes as $size) {
			$options[$size] = ucwords(str_replace(['-', '_'], ' ', $size));
		}
		$options['full'] = esc_html__('Full', 'the-bricksfly');
		return $options;
	}

	public function render()
	{
		$settings      = $this->settings;
		$slide_content = ! empty($settings['slideContent']) ? $settings['slideContent'] : 'text';

		$cast_per_view = function ($v) {
			if ($v === '' || $v === null) return null;
			return is_numeric($v) ? (fmod((float) $v, 1) === 0.0 ? (int) $v : (float) $v) : $v;
		};

		$reverse = ! empty($settings['reverseDirection']);

		/**
		 * Resolve a responsive control across every Bricks breakpoint into a per-range
		 * (min-width keyed) map, replaying Bricks' cascade so blank breakpoints inherit
		 * the correct neighbour. Returns [ minWidth => value, ... ] including the base
		 * range keyed at 0.
		 *
		 * Bricks (default) is DESKTOP-FIRST: a breakpoint's `width` is a MAX-width and a
		 * value cascades DOWN to smaller breakpoints until overridden. Swiper / our JS is
		 * MOBILE-FIRST: keys are MIN-widths, larger key wins. The two cascade in opposite
		 * directions, so we rebuild the cascade rather than copy values 1:1.
		 *
		 * $base_key/$cast let each control read its desktop value and coerce raw strings.
		 * $default is used for any range the Bricks cascade leaves unset, so the returned
		 * map is DENSE: exactly one entry per breakpoint range, keyed by its min-width.
		 * A dense map means consumers never have to guess a gap's value by looking at a
		 * neighbouring range (which would wrongly leak a value across breakpoints).
		 */
		$resolve_responsive = function ($base_key, $cast, $default) use ($settings) {
			$out = [];

			if (! (class_exists('\Bricks\Breakpoints') && is_array(\Bricks\Breakpoints::$breakpoints) && ! empty(\Bricks\Breakpoints::$breakpoints))) {
				return $out;
			}

			$is_mobile_first = ! empty(\Bricks\Breakpoints::$is_mobile_first);

			// Collect raw value per breakpoint (desktop/base reads the unsuffixed key).
			$points = [];
			foreach (\Bricks\Breakpoints::$breakpoints as $bp) {
				$key = isset($bp['key']) ? $bp['key'] : '';
				if (! $key) continue;

				$is_base = ! empty($bp['base']) || $key === 'desktop';
				$raw     = $is_base
					? ($settings[$base_key] ?? null)
					: (isset($settings["{$base_key}:{$key}"]) ? $settings["{$base_key}:{$key}"] : null);

				$points[] = [
					'width' => isset($bp['width']) ? intval($bp['width']) : 0,
					'raw'   => $raw,
				];
			}

			usort($points, function ($a, $b) {
				return $a['width'] <=> $b['width'];
			});

			$count = count($points);
			$resolved = [];

			// Replay the cascade in the direction Bricks fills empty breakpoints.
			$carry = null;
			if ($is_mobile_first) {
				for ($i = 0; $i < $count; $i++) {
					if ($points[$i]['raw'] !== null && $points[$i]['raw'] !== '') $carry = $points[$i]['raw'];
					$resolved[$i] = $carry;
				}
			} else {
				for ($i = $count - 1; $i >= 0; $i--) {
					if ($points[$i]['raw'] !== null && $points[$i]['raw'] !== '') $carry = $points[$i]['raw'];
					$resolved[$i] = $carry;
				}
			}

			// Map each resolved point to a min-width key, filling unset ranges with $default.
			for ($i = 0; $i < $count; $i++) {
				if ($is_mobile_first) {
					$min_width = $points[$i]['width'];
				} else {
					$prev      = ($i > 0) ? $points[$i - 1]['width'] : 0;
					$min_width = $prev > 0 ? $prev + 1 : 0;
				}

				$val = ($resolved[$i] === null || $resolved[$i] === '') ? null : $cast($resolved[$i]);
				if ($val === null) $val = $default;

				$out[$min_width] = $val; // later (larger-index) write wins on key collision
			}

			ksort($out);
			return $out;
		};

		// Coercion helpers for each control type.
		$cast_pv   = $cast_per_view;
		$cast_int  = function ($v) { return ($v === '' || $v === null) ? null : (int) $v; };
		// Tri-state toggle: 'on' => true, 'off' => false, '' / null => null (inherit).
		// Legacy boolean values (from before the select migration) are honoured too.
		$cast_bool = function ($v) {
			if ($v === '' || $v === null) return null;
			if ($v === 'on'  || $v === true  || $v === 1 || $v === '1') return true;
			if ($v === 'off' || $v === false || $v === 0 || $v === '0') return false;
			return (bool) $v;
		};

		// Desktop (top-level) fallbacks — used as each control's cascade default and as the
		// pre-JS / no-breakpoint-data state. Toggles resolve through $cast_bool, applying the
		// control default when the setting is absent / left on "Default".
		$desktop_toggle = function ($key, $default) use ($settings, $cast_bool) {
			$v = $cast_bool($settings[$key] ?? null);
			return $v === null ? $default : $v;
		};

		$desktop_pv  = $cast_per_view($settings['slidesPerView'] ?? 'auto');
		if ($desktop_pv === null) $desktop_pv = 'auto';
		$desktop_sb  = isset($settings['spaceBetween']) && $settings['spaceBetween'] !== '' ? (int) $settings['spaceBetween'] : 30;
		$speed       = isset($settings['speed']) ? (int) $settings['speed'] : 5000;
		$loop        = $desktop_toggle('loop', true);          // control default 'on'
		$autoplay    = $desktop_toggle('autoplay', true);      // control default 'on'
		$delay       = isset($settings['autoplayDelay']) ? (int) $settings['autoplayDelay'] : 0;
		$pause_hover = $desktop_toggle('pauseOnHover', false);
		$show_nav    = $desktop_toggle('showNavigation', false);
		$show_pag    = $desktop_toggle('showPagination', false);

		// Resolve every responsive control into a DENSE per-range map (one entry per breakpoint
		// range), each control's gaps filled with its desktop default so ranges never leak.
		$pv_map    = $resolve_responsive('slidesPerView', $cast_pv,   $desktop_pv);
		$sb_map    = $resolve_responsive('spaceBetween',  $cast_int,  $desktop_sb);
		$speed_map = $resolve_responsive('speed',         $cast_int,  $speed);
		$delay_map = $resolve_responsive('autoplayDelay', $cast_int,  $delay);
		$ap_map    = $resolve_responsive('autoplay',      $cast_bool, $autoplay);
		$loop_map  = $resolve_responsive('loop',          $cast_bool, $loop);
		$hover_map = $resolve_responsive('pauseOnHover',  $cast_bool, $pause_hover);
		$nav_map   = $resolve_responsive('showNavigation', $cast_bool, $show_nav);
		$pag_map   = $resolve_responsive('showPagination', $cast_bool, $show_pag);

		// `slide-width-auto` CSS hook: true if desktop OR any breakpoint uses "auto".
		$has_auto = ($desktop_pv === 'auto') || in_array('auto', $pv_map, true);

		/**
		 * Build a complete Swiper option object for a given set of resolved values. Used
		 * both for the base (top-level) options and for each responsive range the JS
		 * re-inits with. Selectors for nav/pagination are resolved to elements in JS.
		 */
		$build_options = function ($pv, $sb, $sp, $lp, $ap, $dl, $hov, $nav, $pag) use ($reverse) {
			$opts = [
				'slidesPerView' => $pv,
				'spaceBetween'  => $sb,
				'speed'         => $sp,
				'loop'          => $lp,
			];

			if ($ap) {
				$opts['autoplay'] = [
					'delay'                => $dl,
					'disableOnInteraction' => false,
					'reverseDirection'     => $reverse,
					'pauseOnMouseEnter'    => $hov,
				];

				// Marquee mode (delay 0/1) needs freeMode for continuous linear motion.
				if ($dl <= 1) {
					$opts['freeMode'] = ['enabled' => true, 'momentum' => false];
				}
			} else {
				$opts['autoplay'] = false;
			}

			$opts['navigation'] = $nav
				? ['nextEl' => '.aab-arrow-next', 'prevEl' => '.aab-arrow-prev']
				: false;

			$opts['pagination'] = $pag
				? ['el' => '.swiper-pagination', 'clickable' => true]
				: false;

			return $opts;
		};

		// Resolve a per-range value, falling back to the desktop default.
		$pick = function ($map, $default) {
			// Base range (key 0) value if present, else desktop default.
			return array_key_exists(0, $map) ? $map[0] : $default;
		};

		// Base (top-level) options use the smallest-range value (Swiper's floor / pre-JS state).
		$swiper_options = $build_options(
			$pick($pv_map, $desktop_pv),
			$pick($sb_map, $desktop_sb),
			$pick($speed_map, $speed),
			$pick($loop_map, $loop),
			$pick($ap_map, $autoplay),
			$pick($delay_map, $delay),
			$pick($hover_map, $pause_hover),
			$pick($nav_map, $show_nav),
			$pick($pag_map, $show_pag)
		);

		/**
		 * Build the per-range responsive map the JS consumes: { minWidth: {full options} }.
		 * Every distinct min-width across all controls becomes a range; each range's value
		 * for a control is the largest min-width entry <= the range's min-width (mobile-first
		 * resolution), falling back to the desktop default.
		 */
		$range_keys = [];
		foreach ([$pv_map, $sb_map, $speed_map, $delay_map, $ap_map, $loop_map, $hover_map, $nav_map, $pag_map] as $m) {
			foreach (array_keys($m) as $k) $range_keys[$k] = true;
		}
		$range_keys = array_keys($range_keys);
		sort($range_keys);

		$at = function ($map, $min_width, $default) {
			$value = $default;
			foreach ($map as $k => $v) {
				if ($k <= $min_width) $value = $v; else break;
			}
			return $value;
		};

		$responsive = [];
		foreach ($range_keys as $mw) {
			if ($mw === 0) continue; // base range already lives in $swiper_options
			$responsive[$mw] = $build_options(
				$at($pv_map,    $mw, $desktop_pv),
				$at($sb_map,    $mw, $desktop_sb),
				$at($speed_map, $mw, $speed),
				$at($loop_map,  $mw, $loop),
				$at($ap_map,    $mw, $autoplay),
				$at($delay_map, $mw, $delay),
				$at($hover_map, $mw, $pause_hover),
				$at($nav_map,   $mw, $show_nav),
				$at($pag_map,   $mw, $show_pag)
			);
		}

		// Native Swiper breakpoints (layout only) so slidesPerView/spaceBetween still
		// respond instantly without a re-init; the JS layers the behavioral options on top.
		$layout_breakpoints = [];
		foreach ($responsive as $mw => $opts) {
			$layout_breakpoints[$mw] = [
				'slidesPerView' => $opts['slidesPerView'],
				'spaceBetween'  => $opts['spaceBetween'],
			];
		}
		if (! empty($layout_breakpoints)) {
			$swiper_options['breakpoints'] = $layout_breakpoints;
		}

		$auto_class = $has_auto ? ' slide-width-auto' : '';

		// Nav / pagination markup must exist if ANY range enables it (a smaller range may
		// turn it on even when desktop has it off). Swiper toggles the actual modules per range.
		$nav_anywhere = $show_nav || in_array(true, $nav_map, true);
		$pag_anywhere = $show_pag || in_array(true, $pag_map, true);

		$this->set_attribute('_root', 'class', ['aab-brand-slider-wrapper' . $auto_class]);
		$this->set_attribute('_root', 'data-swiper', wp_json_encode($swiper_options));
		if (! empty($responsive)) {
			$this->set_attribute('_root', 'data-swiper-responsive', wp_json_encode($responsive));
		}

		$slides = [];

		if ('image' === $slide_content) {
			// Loop rewritten to read image parameters out of the updated control structure array
			$image_items = ! empty($settings['brandImages']) ? $settings['brandImages'] : [];
			$image_size  = ! empty($settings['imageSize']) ? $settings['imageSize'] : 'medium';

			foreach ($image_items as $item) {
				if (empty($item['image'])) {
					continue;
				}

				$img      = $item['image'];
				$img_id   = is_array($img) ? ($img['id'] ?? 0) : (int) $img;
				$img_url  = '';

				if ($img_id) {
					$src     = wp_get_attachment_image_src($img_id, $image_size);
					$img_url = $src ? $src[0] : '';
				}
				if (! $img_url && is_array($img) && ! empty($img['url'])) {
					$img_url = $img['url'];
				}
				if (! $img_url) {
					continue;
				}

				$alt      = $img_id ? get_post_meta($img_id, '_wp_attachment_image_alt', true) : '';
				$slides[] = '<div class="swiper-slide"><img src="' . esc_url($img_url) . '" alt="' . esc_attr($alt) . '"></div>';
			}
		} else {
			$text_items = ! empty($settings['textSlides']) ? $settings['textSlides'] : [];
			$separator  = '';

			if (! empty($settings['separatorIcon'])) {
				$separator = '<span class="aab-separator-icon">' . self::render_icon($settings['separatorIcon'], ['aria-hidden' => 'true']) . '</span>';
			}

			foreach ($text_items as $item) {
				if (empty($item['text'])) {
					continue;
				}
				$slides[] = '<div class="swiper-slide"><div class="text-slide-content"><div class="title">' . wp_kses_post($item['text']) . '</div>' . $separator . '</div></div>';
			}
		}

		if (empty($slides)) {
			return $this->render_element_placeholder([
				'icon-class' => 'ti-layout-slider',
				'text'       => esc_html__('No slides added.', 'the-bricksfly'),
			]);
		}

		echo '<div ' . wp_kses_post($this->render_attributes('_root')) . '>';
		echo '<div class="swiper">';
		echo '<div class="swiper-wrapper">';
		echo implode('', $slides); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';

		if ($nav_anywhere && count($slides) > 1) {
			echo '<div class="aab-arrow-prev"><i class="fas fa-chevron-left" aria-hidden="true"></i></div>';
			echo '<div class="aab-arrow-next"><i class="fas fa-chevron-right" aria-hidden="true"></i></div>';
		}

		if ($pag_anywhere && count($slides) > 1) {
			echo '<div class="swiper-pagination"></div>';
		}

		echo '</div>';
		echo '</div>';
	}
}
