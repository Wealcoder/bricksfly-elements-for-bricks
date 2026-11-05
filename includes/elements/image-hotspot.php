<?php

if (! defined('ABSPATH')) exit;

class BRICKSFLY_Bricks_Image_Hotspot extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-image-hotspot';
	public $icon         = 'ti-location-pin aab-element-marker';
	public $css_selector = '.aab-image-hotspot';
	public $scripts      = ['bricksflyImageHotspot'];

	public function get_label()
	{
		return esc_html__('Image Hotspot', 'bricksfly-elements-for-bricks');
	}

	public function get_keywords()
	{
		return ['image', 'hotspot', 'tooltip', 'marker', 'pin', 'annotation'];
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style('bricks-font-awesome-6');

		wp_enqueue_style(
			'aab-image-hotspot',
			BRICKSFLY_URL . 'public/build/elements/image-hotspot.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-image-hotspot',
			BRICKSFLY_URL . 'public/build/elements/image-hotspot.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups()
	{
		$this->control_groups['image'] = [
			'title' => esc_html__('Image', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['hotspots'] = [
			'title' => esc_html__('Hotspot', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['tooltip'] = [
			'title' => esc_html__('Tooltip', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['image_style'] = [
			'title' => esc_html__('Image', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['hotspot_style'] = [
			'title' => esc_html__('Hotspot', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['tooltip_style'] = [
			'title' => esc_html__('Tooltip', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];
	}

	public function set_controls()
	{
		$td      = 'bricksfly-elements-for-bricks';
		$icon    = '.aab-hotspot__icon';
		$tooltip = '.aab-hotspot__tooltip';

		/* =========================
		   IMAGE
		========================= */

		$this->controls['image'] = [
			'group'   => 'image',
			'label'   => esc_html__('Image', $td),
			'type'    => 'image',
			'default' => [
				'url' => \Bricks\Builder::get_template_placeholder_image(),
			],
		];

		/* =========================
		   HOTSPOTS
		   Each repeater item is addressed by Bricks as
		   `.repeater-item:nth-child(n)`, so the items live alone inside
		   .aab-image-hotspot__items (the <img> must not be their sibling).
		========================= */

		$this->controls['hotspots'] = [
			'group'         => 'hotspots',
			'label'         => esc_html__('Hotspot Lists', $td),
			'type'          => 'repeater',
			'titleProperty' => 'text',
			'fields'        => [
				'layout'          => [
					'label'   => esc_html__('Hotspot Layout', $td),
					'type'    => 'select',
					'options' => [
						'dot'       => esc_html__('Default', $td),
						'icon'      => esc_html__('Icon', $td),
						'text'      => esc_html__('Text', $td),
						'icon-text' => esc_html__('Icon + Text', $td),
					],
					'default' => 'dot',
				],
				'tooltip_type'    => [
					'label'   => esc_html__('Tooltip Type', $td),
					'type'    => 'select',
					'options' => [
						'tooltip' => esc_html__('Content', $td),
						'link'    => esc_html__('Link', $td),
					],
					'default' => 'tooltip',
				],
				'icon'            => [
					'label'    => esc_html__('Icon', $td),
					'type'     => 'icon',
					'default'  => [
						'library' => 'fontawesome',
						'icon'    => 'fas fa-circle',
					],
					'required' => ['layout', '=', ['icon', 'icon-text']],
				],
				'text'            => [
					'label'       => esc_html__('Text', $td),
					'type'        => 'text',
					'placeholder' => esc_html__('Type your text here', $td),
					'required'    => ['layout', '=', ['text', 'icon-text']],
				],
				'tooltip_content' => [
					'label'    => esc_html__('Tooltip Content', $td),
					'type'     => 'editor',
					'default'  => esc_html__('Tooltip content', $td),
					'required' => ['tooltip_type', '=', 'tooltip'],
				],
				'link'            => [
					'label'    => esc_html__('Link', $td),
					'type'     => 'link',
					'required' => ['tooltip_type', '=', 'link'],
				],
				// No 'selector' => CSS lands on the repeater item itself.
				'pos_left'        => [
					'label'       => esc_html__('Horizontal Offset', $td),
					'type'        => 'number',
					'units'       => true,
					'placeholder' => '50%',
					'css'         => [
						[
							'property' => 'left',
						],
					],
				],
				'pos_top'         => [
					'label'       => esc_html__('Vertical Offset', $td),
					'type'        => 'number',
					'units'       => true,
					'placeholder' => '50%',
					'css'         => [
						[
							'property' => 'top',
						],
					],
				],
			],
			'default'       => [
				[
					'layout'          => 'dot',
					'tooltip_type'    => 'tooltip',
					'tooltip_content' => esc_html__('Tooltip content', $td),
					'pos_left'        => '30%',
					'pos_top'         => '40%',
				],
				[
					'layout'          => 'icon',
					'icon'            => ['library' => 'fontawesome', 'icon' => 'fas fa-plus'],
					'tooltip_type'    => 'tooltip',
					'tooltip_content' => esc_html__('Tooltip content', $td),
					'pos_left'        => '65%',
					'pos_top'         => '60%',
				],
			],
		];

		$this->controls['animation'] = [
			'group'   => 'hotspots',
			'label'   => esc_html__('Animation', $td),
			'type'    => 'select',
			'options' => [
				'beat'  => esc_html__('Soft Beat', $td),
				'pulse' => esc_html__('Pulse', $td),
				'none'  => esc_html__('None', $td),
			],
			'default' => 'pulse',
		];

		$this->controls['animation_speed'] = [
			'group'    => 'hotspots',
			'label'    => esc_html__('Animation Speed (s)', $td),
			'type'     => 'number',
			'min'      => 0.1,
			'max'      => 10,
			'step'     => 0.1,
			'default'  => 3,
			'required' => ['animation', '=', ['beat', 'pulse']],
			'css'      => [
				[
					'property' => '--anim-speed',
					'selector' => $icon,
					'value'    => '%ss',
				],
			],
		];

		$this->controls['pulse_color'] = [
			'group'    => 'hotspots',
			'label'    => esc_html__('Pulse Color', $td),
			'type'     => 'color',
			'required' => ['animation', '=', 'pulse'],
			'css'      => [
				[
					'property' => '--pulse-color',
					'selector' => $icon,
				],
			],
		];

		/* =========================
		   TOOLTIP
		========================= */

		$this->controls['trigger_type'] = [
			'group'   => 'tooltip',
			'label'   => esc_html__('Trigger Type', $td),
			'type'    => 'select',
			'options' => [
				'hover' => esc_html__('Hover', $td),
				'click' => esc_html__('Click', $td),
				'none'  => esc_html__('None (always visible)', $td),
			],
			'default' => 'hover',
		];

		$this->controls['tooltip_position'] = [
			'group'   => 'tooltip',
			'label'   => esc_html__('Position', $td),
			'type'    => 'select',
			'options' => [
				'top'    => esc_html__('Top', $td),
				'bottom' => esc_html__('Bottom', $td),
				'left'   => esc_html__('Left', $td),
				'right'  => esc_html__('Right', $td),
			],
			'default' => 'bottom',
		];

		$this->controls['tooltip_gap'] = [
			'group' => 'tooltip',
			'label' => esc_html__('Gap', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [
				[
					'property' => '--tlp-gap',
					'selector' => $tooltip,
				],
			],
		];

		$this->controls['tooltip_align'] = [
			'group'   => 'tooltip',
			'label'   => esc_html__('Alignment', $td),
			'type'    => 'select',
			'options' => [
				'left'   => esc_html__('Left', $td),
				'center' => esc_html__('Center', $td),
				'right'  => esc_html__('Right', $td),
			],
			'inline'  => true,
			'css'     => [
				[
					'property' => 'text-align',
					'selector' => $tooltip,
				],
			],
		];

		/* =========================
		   IMAGE STYLE
		========================= */

		$this->controls['img_width'] = [
			'group' => 'image_style',
			'label' => esc_html__('Width', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [['property' => 'width', 'selector' => '.aab-image-hotspot']],
		];

		$this->controls['img_height'] = [
			'group' => 'image_style',
			'label' => esc_html__('Height', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [['property' => 'height', 'selector' => '.aab-image-hotspot']],
		];

		$this->controls['img_radius'] = [
			'group' => 'image_style',
			'label' => esc_html__('Border Radius', $td),
			'type'  => 'dimensions',
			'css'   => [['property' => 'border-radius', 'selector' => '.aab-image-hotspot img']],
		];

		/* =========================
		   HOTSPOT STYLE
		========================= */

		$this->controls['hsp_bg'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Background', $td),
			'type'  => 'background',
			'css'   => [['property' => 'background', 'selector' => $icon]],
		];

		// The pulse rings are pseudo-elements of the icon, so the radius
		// must follow the border or the rings stay round around a square dot.
		$this->controls['hsp_border'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Border', $td),
			'type'  => 'border',
			'css'   => [
				['property' => 'border', 'selector' => $icon],
				['property' => 'border-radius', 'selector' => '.aab-image-hotspot--pulse ' . $icon . '::before'],
				['property' => 'border-radius', 'selector' => '.aab-image-hotspot--pulse ' . $icon . '::after'],
			],
		];

		$this->controls['hsp_padding'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Padding', $td),
			'type'  => 'dimensions',
			'css'   => [['property' => 'padding', 'selector' => $icon]],
		];

		$this->controls['dot_width'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Dot Width', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [['property' => 'width', 'selector' => $icon . '--dot']],
		];

		$this->controls['dot_height'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Dot Height', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [['property' => 'height', 'selector' => $icon . '--dot']],
		];

		$this->controls['text_color'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Text Color', $td),
			'type'  => 'color',
			'css'   => [
				['property' => 'color', 'selector' => $icon . '--text, ' . $icon . '--icon-text'],
				['property' => 'fill', 'selector' => $icon . '--text, ' . $icon . '--icon-text'],
			],
		];

		$this->controls['text_typo'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Text Typography', $td),
			'type'  => 'typography',
			'css'   => [['property' => 'typography', 'selector' => $icon . '--text, ' . $icon . '--icon-text']],
		];

		$this->controls['icon_color'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Icon Color', $td),
			'type'  => 'color',
			'css'   => [
				['property' => 'color', 'selector' => $icon . '--icon'],
				['property' => 'fill', 'selector' => $icon . '--icon'],
			],
		];

		$this->controls['icon_size'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Icon Size', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [['property' => 'font-size', 'selector' => $icon . ' svg, ' . $icon . ' i']],
		];

		$this->controls['icon_gap'] = [
			'group' => 'hotspot_style',
			'label' => esc_html__('Icon Gap', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [['property' => 'gap', 'selector' => $icon]],
		];

		/* =========================
		   TOOLTIP STYLE
		========================= */

		// Colour (not a full background) so the arrow, a ::after shape, can share it.
		$this->controls['tlp_bg'] = [
			'group' => 'tooltip_style',
			'label' => esc_html__('Background', $td),
			'type'  => 'color',
			'css'   => [
				['property' => 'background-color', 'selector' => $tooltip],
				['property' => 'background-color', 'selector' => $tooltip . '::after'],
			],
		];

		$this->controls['tlp_width'] = [
			'group' => 'tooltip_style',
			'label' => esc_html__('Width', $td),
			'type'  => 'number',
			'units' => true,
			'css'   => [['property' => 'width', 'selector' => $tooltip]],
		];

		$this->controls['tlp_radius'] = [
			'group' => 'tooltip_style',
			'label' => esc_html__('Border Radius', $td),
			'type'  => 'dimensions',
			'css'   => [['property' => 'border-radius', 'selector' => $tooltip]],
		];

		$this->controls['tlp_padding'] = [
			'group' => 'tooltip_style',
			'label' => esc_html__('Padding', $td),
			'type'  => 'dimensions',
			'css'   => [['property' => 'padding', 'selector' => $tooltip]],
		];
	}

	public function render()
	{
		$s     = $this->settings;
		$items = ! empty($s['hotspots']) && is_array($s['hotspots']) ? $s['hotspots'] : [];

		if (empty($items)) {
			return $this->render_element_placeholder([
				'title' => esc_html__('No hotspot added.', 'bricksfly-elements-for-bricks'),
			]);
		}

		$animation = $s['animation'] ?? 'pulse';
		$animation = in_array($animation, ['beat', 'pulse', 'none'], true) ? $animation : 'pulse';

		$position = $s['tooltip_position'] ?? 'bottom';
		$position = in_array($position, ['top', 'bottom', 'left', 'right'], true) ? $position : 'bottom';

		$trigger = $s['trigger_type'] ?? 'hover';
		$trigger = in_array($trigger, ['hover', 'click', 'none'], true) ? $trigger : 'hover';

		$this->set_attribute('box', 'class', [
			'aab-image-hotspot',
			'aab-image-hotspot--' . $animation,
			'aab-image-hotspot--tlp-' . $position,
		]);

		echo '<div ' . $this->render_attributes('_root') . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bricks escapes attributes.
		echo '<div ' . $this->render_attributes('box') . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$this->render_image($s);

		echo '<div class="aab-image-hotspot__items">';

		foreach ($items as $index => $item) {
			$this->render_item($index, $item, $trigger);
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	private function render_image($s)
	{
		$image = $this->resolve_image($s['image'] ?? []);

		if (! empty($image['id'])) {
			echo wp_get_attachment_image((int) $image['id'], $image['size'], false, ['class' => 'aab-image-hotspot__img']);
		} elseif (! empty($image['url'])) {
			echo '<img class="aab-image-hotspot__img" src="' . esc_url($image['url']) . '" alt="" />';
		}
	}

	/**
	 * Normalise the image control (incl. dynamic data), as Bricks' Image element does.
	 */
	private function resolve_image($image)
	{
		if (empty($image)) {
			return [];
		}

		$image['size'] = empty($image['size']) ? BRICKS_DEFAULT_IMAGE_SIZE : $image['size'];

		if (! empty($image['useDynamicData'])) {
			$images = $this->render_dynamic_data_tag($image['useDynamicData'], 'image', ['size' => $image['size']]);

			if (empty($images[0])) {
				return [];
			}

			if (is_numeric($images[0])) {
				$image['id'] = (int) $images[0];
				unset($image['url']);
			} else {
				$image['id']  = 0;
				$image['url'] = $images[0];
			}
		}

		if (! empty($image['id']) && empty($image['url'])) {
			$image['url'] = wp_get_attachment_image_url($image['id'], $image['size']);
		}

		return $image;
	}

	private function render_item($index, $item, $trigger)
	{
		$layout = $item['layout'] ?? 'dot';
		$layout = in_array($layout, ['dot', 'icon', 'text', 'icon-text'], true) ? $layout : 'dot';
		$type   = $item['tooltip_type'] ?? 'tooltip';

		$this->set_attribute("item-{$index}", 'class', [
			'aab-hotspot',
			'repeater-item',
			'aab-hotspot--' . $trigger,
		]);

		$is_link = 'link' === $type;
		$tag     = 'div';

		if ($is_link) {
			$tag = 'a';
			if (! empty($item['link']['url']) && method_exists($this, 'set_link_attributes')) {
				$this->set_link_attributes("item-{$index}", $item['link']);
			}
		}

		echo '<' . tag_escape($tag) . ' ' . $this->render_attributes("item-{$index}") . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="aab-hotspot__icon aab-hotspot__icon--' . esc_attr($layout) . '">';

		$text = isset($item['text']) ? esc_html($item['text']) : '';
		$icon = ! empty($item['icon']) ? self::render_icon($item['icon'], ['aria-hidden' => 'true']) : '';

		if ('text' === $layout) {
			echo $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		} elseif ('icon' === $layout) {
			echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bricks-rendered icon markup.
		} elseif ('icon-text' === $layout) {
			echo $text . $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>';

		if (! $is_link) {
			echo '<div class="aab-hotspot__tooltip">' . wp_kses_post($item['tooltip_content'] ?? '') . '</div>';
		}

		echo '</' . tag_escape($tag) . '>';
	}
}
