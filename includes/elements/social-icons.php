<?php

if (! defined('ABSPATH')) exit;

class BRICKSFLY_Bricks_Social_Icons extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-social-icons';
	public $icon         = 'ti-share aab-element-marker';
	public $css_selector = '.bricksfly-social-icons';
	public $scripts      = [];

	public function get_label()
	{
		return esc_html__('Social Icons', 'bricksfly-elements-for-bricks');
	}

	public function get_keywords()
	{
		return ['social', 'icons', 'facebook', 'twitter', 'share'];
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style('bricks-font-awesome-6');
		wp_enqueue_style('bricks-font-awesome-6-brands');

		wp_enqueue_style(
			'aab-social-icons',
			BRICKSFLY_URL . 'public/build/elements/social-icons.css',
			[],
			'1.0.0'
		);
	}

	public function set_control_groups()
	{
		$this->control_groups['icons'] = [
			'title' => esc_html__('Icons', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['icon_style'] = [
			'title' => esc_html__('Icon', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['icon_hover'] = [
			'title' => esc_html__('Icon Hover', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];
	}

	public function set_controls()
	{

		// --- Content: Icons ---

		$this->controls['socialIcons'] = [
			'tab'           => 'content',
			'group'         => 'icons',
			'label'         => esc_html__('Social Icons', 'bricksfly-elements-for-bricks'),
			'type'          => 'repeater',
			'titleProperty' => 'label',
			'fields'        => [
				'icon' => [
					'label'   => esc_html__('Icon', 'bricksfly-elements-for-bricks'),
					'type'    => 'icon',
					'default' => [
						'library' => 'fontawesome',
						'icon'    => 'fab fa-wordpress',
					],
				],
				'link' => [
					'label' => esc_html__('Link', 'bricksfly-elements-for-bricks'),
					'type'  => 'link',
				],
				'label' => [
					'label'   => esc_html__('Label', 'bricksfly-elements-for-bricks'),
					'type'    => 'text',
					'default' => esc_html__('Social', 'bricksfly-elements-for-bricks'),
				],
				'itemColor' => [
					'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
					'type'  => 'color',
				],
				'itemBgColor' => [
					'label' => esc_html__('Background Color', 'bricksfly-elements-for-bricks'),
					'type'  => 'color',
				],
			],
			'default' => [
				[
					'icon'  => ['library' => 'fontawesome', 'icon' => 'fab fa-facebook'],
					'label' => 'Facebook',
				],
				[
					'icon'  => ['library' => 'fontawesome', 'icon' => 'fab fa-twitter'],
					'label' => 'Twitter',
				],
				[
					'icon'  => ['library' => 'fontawesome', 'icon' => 'fab fa-youtube'],
					'label' => 'YouTube',
				],
			],
		];

		// $this->controls['iconAlign'] = [
		// 	'tab'   => 'content',
		// 	'group' => 'icons',
		// 	'label' => esc_html__('Alignment', 'bricksfly-elements-for-bricks'),
		// 	'type'  => 'justify-content',
		// 	'css'   => [
		// 		[
		// 			'property' => 'justify-content',
		// 			'selector' => '.bricksfly-social-icons-list',
		// 		],
		// 	],
		// ];

		// $this->controls['iconDirection'] = [
		// 	'tab'     => 'content',
		// 	'group'   => 'icons',
		// 	'label'   => esc_html__('Direction', 'bricksfly-elements-for-bricks'),
		// 	'type'    => 'select',
		// 	'inline'  => true,
		// 	'options' => [
		// 		'row'    => esc_html__('Row', 'bricksfly-elements-for-bricks'),
		// 		'column' => esc_html__('Column', 'bricksfly-elements-for-bricks'),
		// 	],
		// 	'default' => 'row',
		// 	'css'     => [
		// 		[
		// 			'property' => 'flex-direction',
		// 			'selector' => '.bricksfly-social-icons-list',
		// 		],
		// 	],
		// ];


		$this->controls['iconDirection'] = [
			'tab'     => 'content',
			'group'   => 'icons',
			'label'   => esc_html__('Direction', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				'row'    => esc_html__('Row', 'bricksfly-elements-for-bricks'),
				'column' => esc_html__('Column', 'bricksfly-elements-for-bricks'),
			],
			'default' => 'row',
			'css'     => [
				[
					'property' => 'flex-direction',
					'selector' => '.bricksfly-social-icons-list',
				],
			],
		];



		// When direction is ROW → justify-content
		$this->controls['iconAlign'] = [
			'tab'   => 'content',
			'group' => 'icons',
			'label' => esc_html__('Alignment', 'bricksfly-elements-for-bricks'),
			'type'  => 'justify-content',
			'required' => [['iconDirection', '!=', 'column']],
			'css'   => [
				[
					'property' => 'justify-content',
					'selector' => '.bricksfly-social-icons-list',
				],
			],
		];

		// When direction is COLUMN → align-items
		$this->controls['iconAlignColumn'] = [
			'tab'   => 'content',
			'group' => 'icons',
			'label' => esc_html__('Alignment', 'bricksfly-elements-for-bricks'),
			'type'  => 'align-items',
			'required' => [['iconDirection', '=', 'column']],
			'css'   => [
				[
					'property' => 'align-items',
					'selector' => '.bricksfly-social-icons-list',
				],
			],
		];

		// --- Style: Icon ---

		$this->controls['iconColor'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.bricksfly-social-icon',
				],
				[
					'property' => 'fill',
					'selector' => '.bricksfly-social-icon',
				],
			],
		];

		$this->controls['iconBgColor'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__('Background Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => '.bricksfly-social-icon',
				],
			],
		];

		$this->controls['iconSize'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__('Size', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => [
				'px' => ['min' => 6, 'max' => 300],
			],
			'css' => [
				[
					'property' => '--icon-size',
					'selector' => '.bricksfly-social-icon',
				],
			],
		];

		$this->controls['iconPadding'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__('Padding', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => [
				'px' => ['min' => 1, 'max' => 300],
			],
			'css' => [
				[
					'property' => '--icon-padding',
					'selector' => '.bricksfly-social-icon',
				],
			],
		];

		$this->controls['iconSpacing'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__('Spacing', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => [
				'px' => ['min' => 0, 'max' => 100],
			],
			'css' => [
				[
					'property' => 'gap',
					'selector' => '.bricksfly-social-icons-list',
				],
			],
		];

		$this->controls['iconBorder'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__('Border', 'bricksfly-elements-for-bricks'),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.bricksfly-social-icon',
				],
			],
		];

		$this->controls['iconBorderRadius'] = [
			'tab'   => 'style',
			'group' => 'icon_style',
			'label' => esc_html__('Border Radius', 'bricksfly-elements-for-bricks'),
			'type'  => 'number',
			'units' => [
				'px' => ['min' => 0, 'max' => 300],
				'%'  => ['min' => 0, 'max' => 100],
			],
			'css' => [
				[
					'property' => 'border-radius',
					'selector' => '.bricksfly-social-icon',
				],
			],
		];

		// --- Style: Icon Hover ---

		$this->controls['hoverIconColor'] = [
			'tab'   => 'style',
			'group' => 'icon_hover',
			'label' => esc_html__('Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => '.bricksfly-social-icon:hover',
				],
				[
					'property' => 'fill',
					'selector' => '.bricksfly-social-icon:hover',
				],
			],
		];

		$this->controls['hoverIconBgColor'] = [
			'tab'   => 'style',
			'group' => 'icon_hover',
			'label' => esc_html__('Background Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => '.bricksfly-social-icon:hover',
				],
			],
		];

		$this->controls['hoverBorderColor'] = [
			'tab'   => 'style',
			'group' => 'icon_hover',
			'label' => esc_html__('Border Color', 'bricksfly-elements-for-bricks'),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'border-color',
					'selector' => '.bricksfly-social-icon:hover',
				],
			],
		];

		$this->controls['hoverAnimation'] = [
			'tab'     => 'style',
			'group'   => 'icon_hover',
			'label'   => esc_html__('Hover Animation', 'bricksfly-elements-for-bricks'),
			'type'    => 'select',
			'inline'  => true,
			'options' => [
				''          => esc_html__('None', 'bricksfly-elements-for-bricks'),
				'grow'      => esc_html__('Grow', 'bricksfly-elements-for-bricks'),
				'shrink'    => esc_html__('Shrink', 'bricksfly-elements-for-bricks'),
				'pulse'     => esc_html__('Pulse', 'bricksfly-elements-for-bricks'),
				'float-up'  => esc_html__('Float Up', 'bricksfly-elements-for-bricks'),
			],
			'default' => '',
		];
	}


	// public function set_style()
	// {
	// 	$settings = $this->settings;

	// 	$direction = ! empty($settings['iconDirection'])
	// 		? $settings['iconDirection']
	// 		: 'row';

	// 	$align = ! empty($settings['iconAlign'])
	// 		? $settings['iconAlign']
	// 		: 'flex-start';

	// 	$property = $direction === 'column'
	// 		? 'align-items'
	// 		: 'justify-content';

	// 	return [
	// 		[
	// 			'selector' => '.bricksfly-social-icons-list',
	// 			'property' => $property,
	// 			'value'    => $align,
	// 		],
	// 	];
	// }

	public function render()
	{
		$settings = $this->settings;
		$icons    = ! empty($settings['socialIcons']) ? $settings['socialIcons'] : [];

		if (empty($icons)) {
			return $this->render_element_placeholder([
				'icon-class' => 'ti-share',
				'text'       => esc_html__('No social icons added.', 'bricksfly-elements-for-bricks'),
			]);
		}

		$hover_anim = ! empty($settings['hoverAnimation']) ? $settings['hoverAnimation'] : '';

		$this->set_attribute('_root', 'class', ['bricksfly-social-icons']);

		echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');
		echo '<ul class="bricksfly-social-icons-list">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		foreach ($icons as $index => $item) {
			if (empty($item['icon'])) {
				continue;
			}

			$link_key = 'social-link-' . $index;

			// Per-item inline styles
			$inline_styles = '';
			if (! empty($item['itemColor'])) {
				$color = is_array($item['itemColor']) ? \Bricks\Assets::generate_css_color($item['itemColor']) : $item['itemColor'];
				if ($color) {
					$inline_styles .= 'color:' . esc_attr($color) . ';fill:' . esc_attr($color) . ';';
				}
			}
			if (! empty($item['itemBgColor'])) {
				$bg = is_array($item['itemBgColor']) ? \Bricks\Assets::generate_css_color($item['itemBgColor']) : $item['itemBgColor'];
				if ($bg) {
					$inline_styles .= 'background-color:' . esc_attr($bg) . ';';
				}
			}

			$icon_classes = ['bricksfly-social-icon'];
			if ($hover_anim) {
				$icon_classes[] = 'bricksfly-hover-' . $hover_anim;
			}
			$this->set_attribute($link_key, 'class', $icon_classes);

			if ($inline_styles) {
				$this->set_attribute($link_key, 'style', $inline_styles);
			}

			if (! empty($item['link'])) {
				$this->set_link_attributes($link_key, $item['link']);
			} else {
				$this->set_attribute($link_key, 'href', '#');
			}

			$label = ! empty($item['label']) ? $item['label'] : esc_html__('Social', 'bricksfly-elements-for-bricks');

			echo '<li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post('<a ' . $this->render_attributes($link_key) . '>');
			echo '<span class="screen-reader-text">' . esc_html($label) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post(self::render_icon($item['icon'], ['aria-hidden' => 'true']));
			echo '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
