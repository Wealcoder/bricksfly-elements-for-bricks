<?php

if (! defined('ABSPATH')) exit;

class BRICKSFLY_Bricks_Icon_Box extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-icon-box';
	public $icon         = 'ti-layout-media-center-alt aab-element-marker';
	public $css_selector = '.bricksfly-icon-box';
	public $scripts      = ['bricksflyIconBox'];

	public function get_label()
	{
		return esc_html__('Icon Box', 'bricksfly-elements-for-bricks');
	}

	public function get_keywords()
	{
		return ['icon', 'box', 'feature', 'service', 'info'];
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style(
			'aab-icon-box',
			BRICKSFLY_URL . 'public/build/elements/icon-box.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-icon-box',
			BRICKSFLY_URL . 'public/build/elements/icon-box.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups()
	{
		$this->control_groups['content'] = [
			'title' => esc_html__('Content', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['layout'] = [
			'title' => esc_html__('Layout', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['button_content'] = [
			'title' => esc_html__('Button', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['box_style'] = [
			'title' => esc_html__('Box', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['icon_style'] = [
			'title' => esc_html__('Icon', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['title_style'] = [
			'title' => esc_html__('Title', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['desc_style'] = [
			'title' => esc_html__('Description', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['btn_style'] = [
			'title' => esc_html__('Button Style', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['btn_hover_style'] = [
			'title' => esc_html__('Button Hover', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['flip_back_style'] = [
			'title'    => esc_html__('Flip Back', 'bricksfly-elements-for-bricks'),
			'tab'      => 'style',
			'required' => [['layout_preset', '=', 'flip-card']],
		];
	}

	public function set_controls()
	{

		/* =========================
		   CONTENT
		========================= */

		$this->controls['icon'] = [
			'group'   => 'content',
			'label'   => esc_html__('Icon', 'bricksfly-elements-for-bricks'),
			'type'    => 'icon',
			'default' => [
				'library' => 'themify',
				'icon'    => 'ti-star',
			],
		];

		$this->controls['title'] = [
			'group'   => 'content',
			'label'   => esc_html__('Title', 'bricksfly-elements-for-bricks'),
			'type'    => 'text',
			'default' => esc_html__('Icon Box Title', 'bricksfly-elements-for-bricks'),
		];

		$this->controls['title_tag'] = [
			'group'   => 'content',
			'label'   => esc_html__('Title Tag', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p' => 'p',
				'span' => 'span',
				'div' => 'div',
			],
			'default' => 'h3',
			'inline'  => true,
		];

		$this->controls['description'] = [
			'group'   => 'content',
			'label'   => esc_html__('Description', 'bricksfly-elements-for-bricks'),
			'type'    => 'textarea',
			'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis.', 'bricksfly-elements-for-bricks'),
		];

		$this->controls['box_link'] = [
			'group' => 'content',
			'label' => esc_html__('Box Link', 'bricksfly-elements-for-bricks'),
			'type'  => 'link',
		];

		/* =========================
		   LAYOUT
		========================= */

		$this->controls['layout_preset'] = [
			'group'   => 'layout',
			'label'   => esc_html__('Layout', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'default'      => esc_html__('Default', 'bricksfly-elements-for-bricks'),
				'stacked'      => esc_html__('Stacked Card', 'bricksfly-elements-for-bricks'),
				'bordered'     => esc_html__('Bordered', 'bricksfly-elements-for-bricks'),
				'minimal'      => esc_html__('Minimal', 'bricksfly-elements-for-bricks'),
				'floating'     => esc_html__('Floating Icon', 'bricksfly-elements-for-bricks'),
				'gradient-bar' => esc_html__('Gradient Bar', 'bricksfly-elements-for-bricks'),
				'icon-left-line' => esc_html__('Icon + Left Line', 'bricksfly-elements-for-bricks'),
				'zoom-hover'   => esc_html__('Zoom on Hover', 'bricksfly-elements-for-bricks'),
				'flip-card'    => esc_html__('Flip Card', 'bricksfly-elements-for-bricks'),
				'slide-up'     => esc_html__('Slide Up Reveal', 'bricksfly-elements-for-bricks'),
			],
			'default' => 'default',
		];

		$this->controls['direction'] = [
			'group'   => 'layout',
			'label'   => esc_html__('Direction', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'vertical'           => esc_html__('Vertical', 'bricksfly-elements-for-bricks'),
				'horizontal'         => esc_html__('Horizontal', 'bricksfly-elements-for-bricks'),
				'horizontal-reverse' => esc_html__('Horizontal Reverse', 'bricksfly-elements-for-bricks'),
				'vertical-reverse'   => esc_html__('Vertical Reverse', 'bricksfly-elements-for-bricks'),
			],
			'default' => 'vertical',
		];

		$this->controls['align'] = [
			'group'   => 'layout',
			'label'   => esc_html__('Alignment', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'left'   => esc_html__('Left', 'bricksfly-elements-for-bricks'),
				'center' => esc_html__('Center', 'bricksfly-elements-for-bricks'),
				'right'  => esc_html__('Right', 'bricksfly-elements-for-bricks'),
			],
			'default' => 'center',
		];

		$this->controls['icon_position'] = [
			'group'   => 'layout',
			'label'   => esc_html__('Icon Vertical Align', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'top'    => esc_html__('Top', 'bricksfly-elements-for-bricks'),
				'center' => esc_html__('Center', 'bricksfly-elements-for-bricks'),
				'bottom' => esc_html__('Bottom', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'top',
			'required' => [['direction', '=', ['horizontal', 'horizontal-reverse']]],
		];

		$this->controls['content_gap'] = [
			'group' => 'layout',
			'label' => esc_html__('Content Gap', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'gap',
				'selector' => '.bricksfly-icon-box',
			]],
		];

		// Flip card back content
		$this->controls['flip_back_text'] = [
			'group'    => 'layout',
			'label'    => esc_html__('Back Text', 'bricksfly-elements-for-bricks'),
			'type'     => 'textarea',
			'default'  => esc_html__('Hover to see this side! Add any content for the card back.', 'bricksfly-elements-for-bricks'),
			'required' => [['layout_preset', '=', 'flip-card']],
		];

		$this->controls['flip_direction'] = [
			'group'   => 'layout',
			'label'   => esc_html__('Flip Direction', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'horizontal' => esc_html__('Horizontal', 'bricksfly-elements-for-bricks'),
				'vertical'   => esc_html__('Vertical', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'horizontal',
			'required' => [['layout_preset', '=', 'flip-card']],
		];

		// Gradient bar color
		$this->controls['gradient_bar_color'] = [
			'group'    => 'layout',
			'label'    => esc_html__('Bar Gradient', 'bricksfly-elements-for-bricks'),
			'type'     => 'background',
			'required' => [['layout_preset', '=', 'gradient-bar']],
			'css'      => [[
				'property' => 'background',
				'selector' => '.bricksfly-icon-box::before',
			]],
		];

		// Line color
		$this->controls['line_color'] = [
			'group'    => 'layout',
			'label'    => esc_html__('Line Color', 'bricksfly-elements-for-bricks'),
			'type'     => 'color',
			'required' => [['layout_preset', '=', 'icon-left-line']],
			'css'      => [[
				'property' => 'background-color',
				'selector' => '.bricksfly-icon-box__line',
			]],
		];

		/* =========================
		   BUTTON CONTENT
		========================= */

		$this->controls['show_button'] = [
			'group'   => 'button_content',
			'label'   => esc_html__('Show Button', 'bricksfly-elements-for-bricks'),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['btn_text'] = [
			'group'    => 'button_content',
			'label'    => esc_html__('Button Text', 'bricksfly-elements-for-bricks'),
			'type'     => 'text',
			'default'  => esc_html__('Read More', 'bricksfly-elements-for-bricks'),
			'required' => [['show_button', '!=', '']],
		];

		$this->controls['btn_link'] = [
			'group'    => 'button_content',
			'label'    => esc_html__('Button Link', 'bricksfly-elements-for-bricks'),
			'type'     => 'link',
			'required' => [['show_button', '!=', '']],
		];

		$this->controls['btn_icon'] = [
			'group'    => 'button_content',
			'label'    => esc_html__('Button Icon', 'bricksfly-elements-for-bricks'),
			'type'     => 'icon',
			'required' => [['show_button', '!=', '']],
		];

		$this->controls['btn_icon_position'] = [
			'group'   => 'button_content',
			'label'   => esc_html__('Icon Position', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'before' => esc_html__('Before', 'bricksfly-elements-for-bricks'),
				'after'  => esc_html__('After', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'after',
			'required' => [['show_button', '!=', '']],
		];

		$this->controls['btn_style_type'] = [
			'group'   => 'button_content',
			'label'   => esc_html__('Style Type', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'solid'     => esc_html__('Solid', 'bricksfly-elements-for-bricks'),
				'outline'   => esc_html__('Outline', 'bricksfly-elements-for-bricks'),
				'ghost'     => esc_html__('Ghost (Text)', 'bricksfly-elements-for-bricks'),
				'underline' => esc_html__('Underline', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'solid',
			'required' => [['show_button', '!=', '']],
		];

		$this->controls['btn_hover_type'] = [
			'group'   => 'button_content',
			'label'   => esc_html__('Hover Effect', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'none'        => esc_html__('None', 'bricksfly-elements-for-bricks'),
				'fill-left'   => esc_html__('Fill Left', 'bricksfly-elements-for-bricks'),
				'fill-right'  => esc_html__('Fill Right', 'bricksfly-elements-for-bricks'),
				'fill-top'    => esc_html__('Fill Top', 'bricksfly-elements-for-bricks'),
				'fill-bottom' => esc_html__('Fill Bottom', 'bricksfly-elements-for-bricks'),
				'shrink'      => esc_html__('Shrink', 'bricksfly-elements-for-bricks'),
				'grow'        => esc_html__('Grow', 'bricksfly-elements-for-bricks'),
				'icon-slide'  => esc_html__('Icon Slide', 'bricksfly-elements-for-bricks'),
			],
			'default'  => 'none',
			'required' => [['show_button', '!=', '']],
		];

		$this->controls['btn_size'] = [
			'group'   => 'button_content',
			'label'   => esc_html__('Size', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'options' => [
				'xs' => 'XS',
				'sm' => 'SM',
				'md' => 'MD',
				'lg' => 'LG',
			],
			'default'  => 'sm',
			'required' => [['show_button', '!=', '']],
		];

		$this->controls['btn_full_width'] = [
			'group'    => 'button_content',
			'label'    => esc_html__('Full Width', 'bricksfly-elements-for-bricks'),
			'type'     => 'checkbox',
			'required' => [['show_button', '!=', '']],
		];

		/* =========================
		   BOX STYLE
		========================= */

		$this->controls['box_bg'] = [
			'group' => 'box_style',
			'label' => esc_html__('Background', 'bricksfly-elements-for-bricks'),
			'type'  => 'background',
			'css'   => [[
				'property' => 'background',
				'selector' => '.bricksfly-icon-box',
			]],
		];

		$this->controls['box_padding'] = [
			'group' => 'box_style',
			'label' => esc_html__('Padding', 'bricksfly-elements-for-bricks'),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'padding',
				'selector' => '.bricksfly-icon-box',
			]],
		];


		$this->controls['box_border'] = [
			'group' => 'box_style',
			'label' => esc_html__('Border', 'bricksfly-elements-for-bricks'),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.bricksfly-icon-box:not(.bricksfly-icon-box--flip-card) ',
				],
				[
					'property' => 'border-radius',
					'selector' => '.bricksfly-icon-box--flip-card .bricksfly-icon-box__flip-front, .bricksfly-icon-box--flip-card .bricksfly-icon-box__flip-back',
				],
			],
		];

		// $this->controls['box_border'] = [
		// 	'group' => 'box_style',
		// 	'label' => esc_html__('Border', 'bricksfly-elements-for-bricks'),
		// 	'type'  => 'border',
		// 	'css'   => [
		// 		[
		// 			'property' => 'border',
		// 			'selector' => '.bricksfly-icon-box',
		// 		],
		// 		[
		// 			'property' => 'border-radius',
		// 			'selector' => '.bricksfly-icon-box--flip-card .bricksfly-icon-box__flip-front, .bricksfly-icon-box--flip-card .bricksfly-icon-box__flip-back',
		// 		],
		// 	],
		// ];

		$this->controls['box_shadow'] = [
			'group' => 'box_style',
			'label' => esc_html__('Shadow', 'bricksfly-elements-for-bricks'),
			'type'  => 'box-shadow',
			'css'   => [[
				'property' => 'box-shadow',
				'selector' => '.bricksfly-icon-box',
			]],
		];

		$this->controls['box_h_bg'] = [
			'group' => 'box_style',
			'label' => esc_html__('Hover Background', 'bricksfly-elements-for-bricks'),
			'type'  => 'background',
			'css'   => [[
				'property' => 'background',
				'selector' => '.bricksfly-icon-box:hover',
			]],
		];

		$this->controls['box_h_border_color'] = [
			'group' => 'box_style',
			'label' => esc_html__('Hover Border Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'border-color',
				'selector' => '.bricksfly-icon-box:hover',
			]],
		];

		$this->controls['box_h_shadow'] = [
			'group' => 'box_style',
			'label' => esc_html__('Hover Shadow', 'bricksfly-elements-for-bricks'),
			'type'  => 'box-shadow',
			'css'   => [[
				'property' => 'box-shadow',
				'selector' => '.bricksfly-icon-box:hover',
			]],
		];

		$this->controls['box_transition'] = [
			'group'   => 'box_style',
			'label'   => esc_html__('Transition (ms)', 'bricksfly-elements-for-bricks'),
			'type'    => 'number',
			'default' => 300,
			'css'     => [[
				'property' => 'transition-duration',
				'selector' => '.bricksfly-icon-box',
				'value'    => '%sms',
			]],
		];

		/* =========================
		   ICON STYLE
		========================= */

		$this->controls['icon_size'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Size', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'font-size',
				'selector' => '.bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_color'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_bg'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Background', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'background-color',
				'selector' => '.bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_width'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Width / Height', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => true,
			'css'   => [
				[
					'property' => 'width',
					'selector' => '.bricksfly-icon-box__icon',
				],
				[
					'property' => 'height',
					'selector' => '.bricksfly-icon-box__icon',
				],
			],
		];

		$this->controls['icon_border'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Border', 'bricksfly-elements-for-bricks'),
			'type'  => 'border',
			'css'   => [[
				'property' => 'border',
				'selector' => '.bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_padding'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Padding', 'bricksfly-elements-for-bricks'),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'padding',
				'selector' => '.bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_shadow'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Shadow', 'bricksfly-elements-for-bricks'),
			'type'  => 'box-shadow',
			'css'   => [[
				'property' => 'box-shadow',
				'selector' => '.bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_h_color'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Hover Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box:hover .bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_h_bg'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Hover Background', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'background-color',
				'selector' => '.bricksfly-icon-box:hover .bricksfly-icon-box__icon',
			]],
		];

		$this->controls['icon_spacing'] = [
			'group' => 'icon_style',
			'label' => esc_html__('Bottom Spacing', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'margin-bottom',
				'selector' => '.bricksfly-icon-box__icon',
			]],
		];

		/* =========================
		   TITLE STYLE
		========================= */

		$this->controls['title_typo'] = [
			'group' => 'title_style',
			'label' => esc_html__('Typography', 'bricksfly-elements-for-bricks'),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.bricksfly-icon-box__title',
			]],
		];

		$this->controls['title_color'] = [
			'group' => 'title_style',
			'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box__title',
			]],
		];

		$this->controls['title_h_color'] = [
			'group' => 'title_style',
			'label' => esc_html__('Hover Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box:hover .bricksfly-icon-box__title',
			]],
		];

		$this->controls['title_margin'] = [
			'group' => 'title_style',
			'label' => esc_html__('Margin', 'bricksfly-elements-for-bricks'),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'margin',
				'selector' => '.bricksfly-icon-box__title',
			]],
		];

		/* =========================
		   DESCRIPTION STYLE
		========================= */

		$this->controls['desc_typo'] = [
			'group' => 'desc_style',
			'label' => esc_html__('Typography', 'bricksfly-elements-for-bricks'),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.bricksfly-icon-box__desc',
			]],
		];

		$this->controls['desc_color'] = [
			'group' => 'desc_style',
			'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box__desc',
			]],
		];

		$this->controls['desc_h_color'] = [
			'group' => 'desc_style',
			'label' => esc_html__('Hover Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box:hover .bricksfly-icon-box__desc',
			]],
		];

		$this->controls['desc_margin'] = [
			'group' => 'desc_style',
			'label' => esc_html__('Margin', 'bricksfly-elements-for-bricks'),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'margin',
				'selector' => '.bricksfly-icon-box__desc',
			]],
		];

		/* =========================
		   BUTTON STYLE
		========================= */

		$this->controls['btn_typo'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Typography', 'bricksfly-elements-for-bricks'),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.bricksfly-icon-box__btn',
			]],
		];

		$this->controls['btn_color'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box__btn',
			]],
		];

		$this->controls['btn_bg_color'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Background', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'background-color',
				'selector' => '.bricksfly-icon-box__btn',
			]],
		];

		$this->controls['btn_border'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Border', 'bricksfly-elements-for-bricks'),
			'type'  => 'border',
			'css'   => [[
				'property' => 'border',
				'selector' => '.bricksfly-icon-box__btn',
			]],
		];

		$this->controls['btn_padding'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Padding', 'bricksfly-elements-for-bricks'),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'padding',
				'selector' => '.bricksfly-icon-box__btn',
			]],
		];

		$this->controls['btn_shadow'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Shadow', 'bricksfly-elements-for-bricks'),
			'type'  => 'box-shadow',
			'css'   => [[
				'property' => 'box-shadow',
				'selector' => '.bricksfly-icon-box__btn',
			]],
		];

		$this->controls['btn_icon_size'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Icon Size', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'font-size',
				'selector' => '.bricksfly-icon-box__btn-icon',
			]],
		];

		$this->controls['btn_icon_gap'] = [
			'group' => 'btn_style',
			'label' => esc_html__('Icon Gap', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'gap',
				'selector' => '.bricksfly-icon-box__btn',
			]],
		];

		/* =========================
		   BUTTON HOVER STYLE
		========================= */

		$this->controls['btn_h_color'] = [
			'group' => 'btn_hover_style',
			'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-icon-box__btn:hover',
			]],
		];

		$this->controls['btn_h_bg_color'] = [
			'group' => 'btn_hover_style',
			'label' => esc_html__('Background', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'background-color',
				'selector' => '.bricksfly-icon-box__btn:hover',
			]],
		];

		$this->controls['btn_h_border_color'] = [
			'group' => 'btn_hover_style',
			'label' => esc_html__('Border Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'border-color',
				'selector' => '.bricksfly-icon-box__btn:hover',
			]],
		];

		$this->controls['btn_h_shadow'] = [
			'group' => 'btn_hover_style',
			'label' => esc_html__('Shadow', 'bricksfly-elements-for-bricks'),
			'type'  => 'box-shadow',
			'css'   => [[
				'property' => 'box-shadow',
				'selector' => '.bricksfly-icon-box__btn:hover',
			]],
		];

		$this->controls['btn_hover_fill_color'] = [
			'group'    => 'btn_hover_style',
			'label'    => esc_html__('Fill Color', 'bricksfly-elements-for-bricks'),
			'type'     => 'color',
			'required' => [['btn_hover_type', '=', ['fill-left', 'fill-right', 'fill-top', 'fill-bottom']]],
			'css'      => [[
				'property' => 'background-color',
				'selector' => '.bricksfly-icon-box__btn::before',
			]],
		];

		$this->controls['btn_transition'] = [
			'group'   => 'btn_hover_style',
			'label'   => esc_html__('Transition (ms)', 'bricksfly-elements-for-bricks'),
			'type'    => 'number',
			'default' => 300,
			'css'     => [[
				'property' => 'transition-duration',
				'selector' => '.bricksfly-icon-box__btn',
				'value'    => '%sms',
			]],
		];



		/* =========================
			FLIP BACK STYLE
		========================= */

		$this->controls['flip_back_bg'] = [
			'group'    => 'flip_back_style',
			'label'    => esc_html__('Background', 'bricksfly-elements-for-bricks'),
			'type'     => 'background',
			'required' => [['layout_preset', '=', 'flip-card']],
			'css'      => [[
				'property' => 'background',
				'selector' => '.bricksfly-icon-box__flip-back',
			]],
		];

		$this->controls['flip_back_padding'] = [
			'group'    => 'flip_back_style',
			'label'    => esc_html__('Padding', 'bricksfly-elements-for-bricks'),
			'type'     => 'dimensions',
			'required' => [['layout_preset', '=', 'flip-card']],
			'css'      => [[
				'property' => 'padding',
				'selector' => '.bricksfly-icon-box__flip-back',
			]],
		];

		// $this->controls['flip_back_border'] = [
		// 	'group'    => 'flip_back_style',
		// 	'label'    => esc_html__('Border', 'bricksfly-elements-for-bricks'),
		// 	'type'     => 'border',
		// 	'required' => [['layout_preset', '=', 'flip-card']],
		// 	'css'      => [[
		// 		'property' => 'border',
		// 		'selector' => '.bricksfly-icon-box__flip-back',
		// 	]],
		// ];

		$this->controls['flip_back_border_color'] = [
			'group'    => 'flip_back_style',
			'label'    => esc_html__('Border Color', 'bricksfly-elements-for-bricks'),
			'type'     => 'color',
			'required' => [['layout_preset', '=', 'flip-card']],
			'css'      => [[
				'property' => 'border-color',
				'selector' => '.bricksfly-icon-box--flip-card .bricksfly-icon-box__flip-back',
			]],
		];

		$this->controls['flip_back_shadow'] = [
			'group'    => 'flip_back_style',
			'label'    => esc_html__('Shadow', 'bricksfly-elements-for-bricks'),
			'type'     => 'box-shadow',
			'required' => [['layout_preset', '=', 'flip-card']],
			'css'      => [[
				'property' => 'box-shadow',
				'selector' => '.bricksfly-icon-box__flip-back',
			]],
		];

		$this->controls['flip_back_typo'] = [
			'group'    => 'flip_back_style',
			'label'    => esc_html__('Typography', 'bricksfly-elements-for-bricks'),
			'type'     => 'typography',
			'required' => [['layout_preset', '=', 'flip-card']],
			'css'      => [[
				'property' => 'typography',
				'selector' => '.bricksfly-icon-box__flip-back .bricksfly-icon-box__desc',
			]],
		];

		// $this->controls['flip_back_color'] = [
		// 	'group'    => 'flip_back_style',
		// 	'label'    => esc_html__('Text Color', 'bricksfly-elements-for-bricks'),
		// 	'type'     => 'color',
		// 	'required' => [['layout_preset', '=', 'flip-card']],
		// 	'css'      => [[
		// 		'property' => 'color',
		// 		'selector' => '.bricksfly-icon-box__flip-back .bricksfly-icon-box__desc',
		// 	]],
		// ];

		$this->controls['flip_back_text_margin'] = [
			'group'    => 'flip_back_style',
			'label'    => esc_html__('Text Margin', 'bricksfly-elements-for-bricks'),
			'type'     => 'dimensions',
			'required' => [['layout_preset', '=', 'flip-card']],
			'css'      => [[
				'property' => 'margin',
				'selector' => '.bricksfly-icon-box__flip-back .bricksfly-icon-box__desc',
			]],
		];
	}

	public function render()
	{
		$s = $this->settings;

		$layout    = $s['layout_preset'] ?? 'default';
		$direction = $s['direction'] ?? 'vertical';
		$align     = $s['align'] ?? 'center';
		$icon_pos  = $s['icon_position'] ?? 'top';

		// Flip card has its own render
		if ($layout === 'flip-card') {
			$this->render_flip_card($s);
			return;
		}

		// Box classes
		$box_classes = [
			'bricksfly-icon-box',
			'bricksfly-icon-box--' . $layout,
			'bricksfly-icon-box--' . $direction,
			'bricksfly-icon-box--align-' . $align,
		];

		if (in_array($direction, ['horizontal', 'horizontal-reverse'], true)) {
			$box_classes[] = 'bricksfly-icon-box--icon-' . $icon_pos;
		}

		$this->set_attribute('box', 'class', $box_classes);

		// Box link
		$has_box_link = ! empty($s['box_link']['url']);

		echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');
		echo wp_kses_post('<div ' . $this->render_attributes('box') . '>');

		// Icon
		if (! empty($s['icon'])) {
			echo '<div class="bricksfly-icon-box__icon">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post(self::render_icon($s['icon']));
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Line element for icon-left-line layout
		if ($layout === 'icon-left-line') {
			echo '<div class="bricksfly-icon-box__line"></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Content wrapper
		echo '<div class="bricksfly-icon-box__content">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Title
		$tag = $s['title_tag'] ?? 'h3';
		$tag = in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div'], true) ? $tag : 'h3';
		if (! empty($s['title'])) {
			echo '<' . tag_escape($tag) . ' class="bricksfly-icon-box__title">' . esc_html($s['title']) . '</' . tag_escape($tag) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Description
		if (! empty($s['description'])) {
			echo '<p class="bricksfly-icon-box__desc">' . esc_html($s['description']) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Button
		if (! empty($s['show_button'])) {
			$this->render_button($s);
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Slide-up overlay
		if ($layout === 'slide-up') {
			echo '<div class="bricksfly-icon-box__slide-overlay">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if (! empty($s['description'])) {
				echo '<p class="bricksfly-icon-box__desc">' . esc_html($s['description']) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if (! empty($s['show_button'])) {
				$this->render_button($s);
			}
			echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Box link overlay
		if ($has_box_link) {
			$this->set_attribute('box-link', 'class', 'bricksfly-icon-box__link-overlay');
			$this->set_attribute('box-link', 'href', esc_url($s['box_link']['url']));
			if (! empty($s['box_link']['newTab'])) {
				$this->set_attribute('box-link', 'target', '_blank');
				$this->set_attribute('box-link', 'rel', 'noopener noreferrer');
			}
			echo wp_kses_post('<a ' . $this->render_attributes('box-link') . ' aria-label="' . esc_attr($s['title'] ?? '') . '"></a>');
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_flip_card($s)
	{
		$flip_dir  = $s['flip_direction'] ?? 'horizontal';
		$back_text = $s['flip_back_text'] ?? '';

		$box_classes = [
			'bricksfly-icon-box',
			'bricksfly-icon-box--flip-card',
			'bricksfly-icon-box--flip-' . $flip_dir,
			'bricksfly-icon-box--align-' . ($s['align'] ?? 'center'),
		];

		$this->set_attribute('box', 'class', $box_classes);

		$tag = $s['title_tag'] ?? 'h3';
		$tag = in_array($tag, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div'], true) ? $tag : 'h3';

		echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');
		echo wp_kses_post('<div ' . $this->render_attributes('box') . '>');

		// Front
		echo '<div class="bricksfly-icon-box__flip-front">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if (! empty($s['icon'])) {
			echo wp_kses_post('<div class="bricksfly-icon-box__icon">' . self::render_icon($s['icon']) . '</div>');
		}
		if (! empty($s['title'])) {
			echo '<' . tag_escape($tag) . ' class="bricksfly-icon-box__title">' . esc_html($s['title']) . '</' . tag_escape($tag) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if (! empty($s['description'])) {
			echo '<p class="bricksfly-icon-box__desc">' . esc_html($s['description']) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Back
		echo '<div class="bricksfly-icon-box__flip-back">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if (! empty($back_text)) {
			echo '<p class="bricksfly-icon-box__desc">' . esc_html($back_text) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if (! empty($s['show_button'])) {
			$this->render_button($s);
		}
		// Box link on back
		if (! empty($s['box_link']['url'])) {
			echo '<a class="bricksfly-icon-box__link-overlay" href="' . esc_url($s['box_link']['url']) . '"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			if (! empty($s['box_link']['newTab'])) {
				echo ' target="_blank" rel="noopener noreferrer"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo ' aria-label="' . esc_attr($s['title'] ?? '') . '"></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function render_button($s)
	{
		$btn_style  = $s['btn_style_type'] ?? 'solid';
		$btn_hover  = $s['btn_hover_type'] ?? 'none';
		$btn_size   = $s['btn_size'] ?? 'sm';
		$icon_pos   = $s['btn_icon_position'] ?? 'after';

		$btn_classes = [
			'bricksfly-icon-box__btn',
			'bricksfly-icon-box__btn--' . $btn_style,
			'bricksfly-icon-box__btn--' . $btn_size,
		];

		if ($btn_hover !== 'none') {
			$btn_classes[] = 'bricksfly-icon-box__btn--hover-' . $btn_hover;
		}

		if (! empty($s['btn_full_width'])) {
			$btn_classes[] = 'bricksfly-icon-box__btn--full';
		}

		$btn_classes[] = 'bricksfly-icon-box__btn--icon-' . $icon_pos;

		$this->set_attribute('btn', 'class', $btn_classes);

		if (! empty($s['btn_link']['url'])) {
			$this->set_attribute('btn', 'href', esc_url($s['btn_link']['url']));
			if (! empty($s['btn_link']['newTab'])) {
				$this->set_attribute('btn', 'target', '_blank');
				$this->set_attribute('btn', 'rel', 'noopener noreferrer');
			}
		}

		echo wp_kses_post('<a ' . $this->render_attributes('btn') . '>');

		if (! empty($s['btn_icon']) && $icon_pos === 'before') {
			echo wp_kses_post('<span class="bricksfly-icon-box__btn-icon">' . self::render_icon($s['btn_icon']) . '</span>');
		}

		echo '<span class="bricksfly-icon-box__btn-text">' . esc_html($s['btn_text'] ?? esc_html__('Read More', 'bricksfly-elements-for-bricks')) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if (! empty($s['btn_icon']) && $icon_pos === 'after') {
			echo wp_kses_post('<span class="bricksfly-icon-box__btn-icon">' . self::render_icon($s['btn_icon']) . '</span>');
		}

		echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
