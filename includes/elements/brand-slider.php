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
		return esc_html__('Brand Slider', 'bricksfly');
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
			'title' => esc_html__('Content', 'bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['slider_options'] = [
			'title' => esc_html__('Slider Options', 'bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['items_style'] = [
			'title'    => esc_html__('Items', 'bricksfly'),
			'tab'      => 'style',
		];

		$this->control_groups['image_style'] = [
			'title'    => esc_html__('Image', 'bricksfly'),
			'tab'      => 'style',
			'required' => ['slideContent', '=', 'image'],
		];

		$this->control_groups['text_style'] = [
			'title'    => esc_html__('Text', 'bricksfly'),
			'tab'      => 'style',
			'required' => ['slideContent', '=', 'text'],
		];

		$this->control_groups['nav_style'] = [
			'title' => esc_html__('Navigation', 'bricksfly'),
			'tab'   => 'style',
		];
	}

	public function set_controls()
	{
		// --- Content ---

		$this->controls['slideContent'] = [
			'tab'     => 'content',
			'group'   => 'content',
			'label'   => esc_html__('Slide Content', 'bricksfly'),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'text'  => esc_html__('Text', 'bricksfly'),
				'image' => esc_html__('Image', 'bricksfly'),
			],
			'default' => 'text',
		];

		// Image Repeater (Updated from image-gallery to match text list layout structure)
		$this->controls['brandImages'] = [
			'tab'           => 'content',
			'group'         => 'content',
			'label'         => esc_html__('Image List', 'bricksfly'),
			'type'          => 'repeater',
			'titleProperty' => 'imageTitle',
			'fields'        => [
				'imageTitle' => [
					'label' => esc_html__('Image Title', 'bricksfly'),
					'type'  => 'text',
				],

				'image' => [
					'label' => esc_html__('Choose Image', 'bricksfly'),
					'type'  => 'image',
				],
			],
			'default'  => [],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['imageSize'] = [
			'tab'      => 'content',
			'group'    => 'content',
			'label'    => esc_html__('Image Size', 'bricksfly'),
			'type'     => 'select',
			'options'  => $this->get_image_sizes(),
			'default'  => 'medium',
			'required' => ['slideContent', '=', 'image'],
		];

		// Text repeater
		$this->controls['textSlides'] = [
			'tab'           => 'content',
			'group'         => 'content',
			'label'         => esc_html__('Text List', 'bricksfly'),
			'type'          => 'repeater',
			'titleProperty' => 'text',
			'fields'        => [
				'text' => [
					'label'   => esc_html__('Text', 'bricksfly'),
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
			'label'    => esc_html__('Text Separator Icon', 'bricksfly'),
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
			'label'       => esc_html__('Slides Per View', 'bricksfly'),
			'type'        => 'text',
			'default'     => 'auto',
			'description' => esc_html__('Number or "auto". Click the device icon to set per breakpoint.', 'bricksfly'),
			'breakpoints' => true,
		];

		$this->controls['spaceBetween'] = [
			'tab'         => 'content',
			'group'       => 'slider_options',
			'label'       => esc_html__('Space Between Item(px)', 'bricksfly'),
			'type'        => 'number',
			'default'     => 0,
			'breakpoints' => true,
		];

		$this->controls['spaceBetweenIconText'] = [
			'tab'         => 'content',
			'group'       => 'slider_options',
			'label'       => esc_html__('Space Between Icon and Text (px)', 'bricksfly'),
			'type'        => 'number',
			'default'     => 0,
			'breakpoints' => true,
		];

		$this->controls['speed'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__('Speed (ms)', 'bricksfly'),
			'type'    => 'number',
			'default' => 5000,
		];

		$this->controls['autoplay'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__('Autoplay', 'bricksfly'),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['autoplayDelay'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__('Autoplay Delay (ms)', 'bricksfly'),
			'type'     => 'number',
			'default'  => 0, // 0 is best for true continuous linear sliders
			'description' => esc_html__('Set to 0 or 1 for a continuous marquee effect.', 'bricksfly'),
			'required' => ['autoplay', '!=', ''],
		];

		$this->controls['loop'] = [
			'tab'     => 'content',
			'group'   => 'slider_options',
			'label'   => esc_html__('Loop', 'bricksfly'),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['pauseOnHover'] = [
			'tab'      => 'content',
			'group'    => 'slider_options',
			'label'    => esc_html__('Pause on Hover', 'bricksfly'),
			'type'     => 'checkbox',
			'required' => ['autoplay', '!=', ''],
		];

		$this->controls['showNavigation'] = [
			'tab'   => 'content',
			'group' => 'slider_options',
			'label' => esc_html__('Show Navigation', 'bricksfly'),
			'type'  => 'checkbox',
		];

		$this->controls['showPagination'] = [
			'tab'   => 'content',
			'group' => 'slider_options',
			'label' => esc_html__('Show Pagination', 'bricksfly'),
			'type'  => 'checkbox',
		];

		$this->controls['reverseDirection'] = [
			'tab'   => 'content',
			'group' => 'slider_options',
			'label' => esc_html__('Reverse Direction', 'bricksfly'),
			'type'  => 'checkbox',
		];

		// Items Style

		$this->controls['icon_position'] = [
			'tab'      => 'style',
			'group'    => 'items_style',
			'label'    => esc_html__('Icon Alignment', 'bricksfly'),
			'type'     => 'select',
			'inline'   => true,
			'options'  => [
				'row-reverse'   => esc_html__('Left', 'bricksfly'),
				'row' => esc_html__('Right', 'bricksfly'),
			],
			'default'  => 'row',
			
			'css'      => [
				['property' => 'flex-direction', 'selector' => '.aab-brand-slider-wrapper .text-slide-content'],
			],
			
		];


		$this->controls['items_alignment'] = [
			'tab'      => 'style',
			'group'    => 'items_style',
			'label'    => esc_html__('Items Alignment', 'bricksfly'),
			'type'     => 'select',
			'inline'   => true,
			'options'  => [
				'left'   => esc_html__('Left', 'bricksfly'),
				'center' => esc_html__('Center', 'bricksfly'),
				'right'  => esc_html__('Right', 'bricksfly'),
			],
			'default'  => 'left',
			
			'css'      => [
				['property' => 'justify-content', 'selector' => '.aab-brand-slider-wrapper .swiper-slide'],
			],
			
		];
		// --- Style: Image ---

		$this->controls['imgWidth'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Width', 'bricksfly'),
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
			'label'    => esc_html__('Height', 'bricksfly'),
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
			'label'    => esc_html__('Object Fit', 'bricksfly'),
			'type'     => 'select',
			'options'  => [
				''           => esc_html__('Default', 'bricksfly'),
				'fill'       => esc_html__('Fill', 'bricksfly'),
				'contain'    => esc_html__('Contain', 'bricksfly'),
				'cover'      => esc_html__('Cover', 'bricksfly'),
				'none'       => esc_html__('None', 'bricksfly'),
				'scale-down' => esc_html__('Scale Down', 'bricksfly'),
			],
			'css'      => [['property' => 'object-fit', 'selector' => '.swiper-slide img']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemBgColor'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Background Color', 'bricksfly'),
			'type'     => 'color',
			'css'      => [['property' => 'background-color', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemBorder'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Border', 'bricksfly'),
			'type'     => 'border',
			'css'      => [['property' => 'border', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemBorderRadius'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Border Radius', 'bricksfly'),
			'type'     => 'dimensions',
			'css'      => [['property' => 'border-radius', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		$this->controls['itemPadding'] = [
			'tab'      => 'style',
			'group'    => 'image_style',
			'label'    => esc_html__('Padding', 'bricksfly'),
			'type'     => 'dimensions',
			'css'      => [['property' => 'padding', 'selector' => '.swiper-slide']],
			'required' => ['slideContent', '=', 'image'],
		];

		// --- Style: Text ---

		$this->controls['textColor'] = [
			'tab'      => 'style',
			'group'    => 'text_style',
			'label'    => esc_html__('Text Color', 'bricksfly'),
			'type'     => 'color',
			'css'      => [['property' => 'color', 'selector' => '.title']],
			'required' => ['slideContent', '=', 'text'],
		];

		$this->controls['textTypography'] = [
			'tab'      => 'style',
			'group'    => 'text_style',
			'label'    => esc_html__('Typography', 'bricksfly'),
			'type'     => 'typography',
			'css'      => [['property' => 'font', 'selector' => '.title']],
			'required' => ['slideContent', '=', 'text'],
		];

		$this->controls['separatorColor'] = [
			'tab'      => 'style',
			'group'    => 'text_style',
			'label'    => esc_html__('Separator Color', 'bricksfly'),
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
			'label'    => esc_html__('Separator Size', 'bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 6, 'max' => 300]],
			'css'      => [['property' => 'font-size', 'selector' => '.aab-separator-icon']],
			'required' => ['slideContent', '=', 'text'],
		];

		// --- Style: Navigation ---

		$this->controls['navColor'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Color', 'bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'color', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '!=', ''],
		];

		$this->controls['navBg'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Background', 'bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'background-color', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '!=', ''],
		];

		$this->controls['navSize'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Size', 'bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 10, 'max' => 100]],
			'css'      => [
				['property' => 'width', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
				['property' => 'height', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '!=', ''],
		];

		$this->controls['navBorder'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Border', 'bricksfly'),
			'type'     => 'border',
			'css'      => [
				['property' => 'border', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '!=', ''],
		];

		// --- Arrow Position ---

		$this->controls['arrowPositionSeparator'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Arrow Position', 'bricksfly'),
			'type'     => 'separator',
			'required' => ['showNavigation', '!=', ''],
		];

		$this->controls['arrowHOffset'] = [
			'tab'         => 'style',
			'group'       => 'nav_style',
			'label'       => esc_html__('Horizontal Offset', 'bricksfly'),
			'type'        => 'number',
			'units'       => [
				'px' => ['min' => -200, 'max' => 200],
				'%'  => ['min' => -50, 'max' => 50],
			],
			'description' => esc_html__('Positive: inside the slider. Negative: outside.', 'bricksfly'),
			'css'         => [
				['property' => 'left',  'selector' => '.aab-arrow-prev'],
				['property' => 'right', 'selector' => '.aab-arrow-next'],
			],
			'required'    => ['showNavigation', '!=', ''],
		];

		$this->controls['arrowVOffset'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Vertical Position', 'bricksfly'),
			'type'     => 'number',
			'units'    => [
				'%'  => ['min' => 0,    'max' => 100],
				'px' => ['min' => -200, 'max' => 1000],
			],
			'css'      => [
				['property' => 'top', 'selector' => '.aab-arrow-prev, .aab-arrow-next'],
			],
			'required' => ['showNavigation', '!=', ''],
		];

		$this->controls['paginationColor'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Pagination Color', 'bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'background-color', 'selector' => '.swiper-pagination-bullet'],
			],
			'required' => ['showPagination', '!=', ''],
		];

		$this->controls['paginationActiveColor'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Pagination Active Color', 'bricksfly'),
			'type'     => 'color',
			'css'      => [
				['property' => 'background-color', 'selector' => '.swiper-pagination-bullet-active'],
			],
			'required' => ['showPagination', '!=', ''],
		];

		$this->controls['paginationSize'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Pagination Dot Size', 'bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 2, 'max' => 60]],
			'css'      => [
				['property' => 'width',  'selector' => '.swiper-pagination-bullet'],
				['property' => 'height', 'selector' => '.swiper-pagination-bullet'],
			],
			'required' => ['showPagination', '!=', ''],
		];

		$this->controls['paginationActiveSize'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Active Dot Size', 'bricksfly'),
			'type'     => 'number',
			'units'    => ['px' => ['min' => 2, 'max' => 80]],
			'css'      => [
				['property' => 'width',  'selector' => '.swiper-pagination-bullet-active'],
				['property' => 'height', 'selector' => '.swiper-pagination-bullet-active'],
			],
			'required' => ['showPagination', '!=', ''],
		];

		$this->controls['paginationAlign'] = [
			'tab'      => 'style',
			'group'    => 'nav_style',
			'label'    => esc_html__('Pagination Alignment', 'bricksfly'),
			'type'     => 'select',
			'inline'   => true,
			'options'  => [
				'left'   => esc_html__('Left', 'bricksfly'),
				'center' => esc_html__('Center', 'bricksfly'),
				'right'  => esc_html__('Right', 'bricksfly'),
			],
			'default'  => 'center',
			'css'      => [
				['property' => 'justify-content', 'selector' => '.swiper-pagination-bullets'],
			],
			'required' => ['showPagination', '!=', ''],
		];
	}

	private function get_image_sizes()
	{
		$sizes   = get_intermediate_image_sizes();
		$options = [];
		foreach ($sizes as $size) {
			$options[$size] = ucwords(str_replace(['-', '_'], ' ', $size));
		}
		$options['full'] = esc_html__('Full', 'bricksfly');
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

		$desktop_pv = $cast_per_view($settings['slidesPerView'] ?? 'auto');
		$desktop_sb = isset($settings['spaceBetween']) && $settings['spaceBetween'] !== '' ? (int) $settings['spaceBetween'] : 30;

		$breakpoint_options = [];
		$has_auto           = ($desktop_pv === 'auto');

		if (class_exists('\Bricks\Breakpoints') && is_array(\Bricks\Breakpoints::$breakpoints) && ! empty(\Bricks\Breakpoints::$breakpoints)) {
			$brk_list = array_values(\Bricks\Breakpoints::$breakpoints);
			$brk_count = count($brk_list);

			for ($index = 0; $index < $brk_count; $index++) {
				$bp  = $brk_list[$index];
				$key = isset($bp['key']) ? $bp['key'] : '';

				if (! $key || $key === 'desktop') {
					continue;
				}

				$next_smaller = isset($brk_list[$index + 1]) ? $brk_list[$index + 1] : null;
				$min_width    = ($next_smaller && ! empty($next_smaller['width'])) ? intval($next_smaller['width']) + 1 : 1;

				$pv_raw = isset($settings["slidesPerView:{$key}"]) ? $settings["slidesPerView:{$key}"] : null;
				$sb_raw = isset($settings["spaceBetween:{$key}"]) ? $settings["spaceBetween:{$key}"] : null;

				$bp_opts = [];
				if ($pv_raw !== null && $pv_raw !== '') {
					$pv = $cast_per_view($pv_raw);
					if ($pv !== null) {
						$bp_opts['slidesPerView'] = $pv;
						if ($pv === 'auto') $has_auto = true;
					}
				}
				if ($sb_raw !== null && $sb_raw !== '') {
					$bp_opts['spaceBetween'] = intval($sb_raw);
				}

				if (! empty($bp_opts)) {
					$breakpoint_options[$min_width] = $bp_opts;
				}
			}
		}

		if (! empty($breakpoint_options)) {
			ksort($breakpoint_options);
		}

		$speed       = isset($settings['speed']) ? (int) $settings['speed'] : 5000;
		$loop        = ! empty($settings['loop']);
		$autoplay    = ! empty($settings['autoplay']);
		$delay       = isset($settings['autoplayDelay']) ? (int) $settings['autoplayDelay'] : 0;
		$pause_hover = ! empty($settings['pauseOnHover']);
		$show_nav    = ! empty($settings['showNavigation']);
		$show_pag    = ! empty($settings['showPagination']);
		$reverse     = ! empty($settings['reverseDirection']);

		$swiper_options = [
			'slidesPerView' => $desktop_pv ?? 'auto',
			'spaceBetween'  => $desktop_sb,
			'speed'         => $speed,
			'loop'          => $loop,
		];

		if (! empty($breakpoint_options)) {
			$swiper_options['breakpoints'] = $breakpoint_options;
		}

		if ($autoplay) {
			$swiper_options['autoplay'] = [
				'delay'                => $delay,
				'disableOnInteraction' => false,
				'reverseDirection'     => $reverse,
				'pauseOnMouseEnter'    => $pause_hover,
			];

			// CRITICAL FIX: If running a marquee (delay 0/1), freeMode must be true
			if ($delay <= 1) {
				$swiper_options['freeMode'] = [
					'enabled'   => true,
					'momentum'  => false,
				];
			}
		}

		if ($show_nav) {
			$swiper_options['navigation'] = [
				'nextEl' => '.aab-arrow-next',
				'prevEl' => '.aab-arrow-prev',
			];
		}

		if ($show_pag) {
			$swiper_options['pagination'] = [
				'el'        => '.swiper-pagination',
				'clickable' => true,
			];
		}

		$auto_class = $has_auto ? ' slide-width-auto' : '';

		$this->set_attribute('_root', 'class', ['aab-brand-slider-wrapper' . $auto_class]);
		$this->set_attribute('_root', 'data-swiper', wp_json_encode($swiper_options));

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
				'text'       => esc_html__('No slides added.', 'bricksfly'),
			]);
		}

		echo '<div ' . wp_kses_post($this->render_attributes('_root')) . '>';
		echo '<div class="swiper">';
		echo '<div class="swiper-wrapper">';
		echo implode('', $slides); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';

		if ($show_nav && count($slides) > 1) {
			echo '<div class="aab-arrow-prev"><i class="fas fa-chevron-left" aria-hidden="true"></i></div>';
			echo '<div class="aab-arrow-next"><i class="fas fa-chevron-right" aria-hidden="true"></i></div>';
		}

		if ($show_pag && count($slides) > 1) {
			echo '<div class="swiper-pagination"></div>';
		}

		echo '</div>';
		echo '</div>';
	}
}
