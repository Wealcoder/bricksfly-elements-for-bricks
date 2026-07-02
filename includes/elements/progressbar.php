<?php

if (! defined('ABSPATH')) exit;

class AAB_Bricks_Progressbar extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-progressbar';
	public $icon         = 'ti-stats-up aab-element-marker';
	public $css_selector = '.aab-progressbar';
	public $scripts      = ['aabProgressbar'];

	public function get_label()
	{
		return esc_html__('Progress Bar', 'the-bricksfly');
	}

	public function get_keywords()
	{
		return ['progress', 'bar', 'progressbar', 'skill', 'percentage', 'circle', 'line', 'dot'];
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style(
			'aab-progressbar',
			AAB_ADDONS_URL . 'public/build/elements/progressbar.css',
			[],
			AAB_ADDONS_VERSION
		);

		wp_enqueue_script(
			'aab-progressbar-lib',
			AAB_ADDONS_URL . 'public/js/lib/progressbar.min.js',
			[],
			AAB_ADDONS_VERSION,
			true
		);

		wp_enqueue_script(
			'aab-progressbar',
			AAB_ADDONS_URL . 'public/build/elements/progressbar.js',
			['aab-progressbar-lib', 'bricks-scripts'],
			AAB_ADDONS_VERSION,
			true
		);
	}

	public function set_control_groups()
	{
		$this->control_groups['progressbar'] = [
			'title' => esc_html__('Progress Bar', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['progressbar_style'] = [
			'title' => esc_html__('Progress Bar', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['percentage_style'] = [
			'title'    => esc_html__('Percentage', 'the-bricksfly'),
			'tab'      => 'style',
			'required' => ['display_percentage', '=', true],
		];
	}

	public function set_controls()
	{

		/* =========================
		   PROGRESS BAR (CONTENT)
		========================= */

		// Layout style — line / circle / dots
		$this->controls['element_list'] = [
			'group'   => 'progressbar',
			'label'   => esc_html__('Layout', 'the-bricksfly'),
			'type'    => 'select',
			'default' => '1',
			'options' => [
				'1' => esc_html__('Line', 'the-bricksfly'),
				'2' => esc_html__('Circle', 'the-bricksfly'),
				'3' => esc_html__('Dots', 'the-bricksfly'),
			],
			'inline'  => true,
		];

		$this->controls['percentage'] = [
			'group'   => 'progressbar',
			'label'   => esc_html__('Percentage', 'the-bricksfly'),
			'type'    => 'number',
			'min'     => 0,
			'max'     => 100,
			'step'    => 1,
			'default' => 50,
		];

		$this->controls['display_percentage'] = [
			'group'    => 'progressbar',
			'label'    => esc_html__('Display Percentage', 'the-bricksfly'),
			'type'     => 'checkbox',
			'default'  => true,
			'required' => ['element_list', '!=', '3'],
		];

		/* =========================
		   PROGRESS BAR STYLE
		========================= */

		// Active/progress color — applies to all 3 layouts.
		// Line/Circle: paints the SVG progress-path stroke (CSS overrides the
		// stroke attribute set by ProgressBar.js).
		// Dots: paints the dot border.
		$this->controls['color'] = [
			'group'   => 'progressbar_style',
			'label'   => esc_html__('Color', 'the-bricksfly'),
			'type'    => 'color',
			'default' => ['hex' => '#7DDED8'],
			'css'     => [
				[
					'property' => 'stroke',
					'selector' => '&.style-1 .progressbar > svg > path:nth-of-type(2)',
				],
				[
					'property' => 'stroke',
					'selector' => '&.style-2 .progressbar > svg > path:nth-of-type(2)',
				],
				[
					'property' => 'border-color',
					'selector' => '&.style-3 .dot',
				],
			],
		];

		// Trail/background color — applies to all 3 layouts.
		// Line/Circle: paints the SVG trail-path stroke.
		// Dots: paints the active dot fill.
		$this->controls['bg_color'] = [
			'group' => 'progressbar_style',
			'label' => esc_html__('Background Color', 'the-bricksfly'),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'stroke',
					'selector' => '&.style-1 .progressbar > svg > path:nth-of-type(1)',
				],
				[
					'property' => 'stroke',
					'selector' => '&.style-2 .progressbar > svg > path:nth-of-type(1)',
				],
				[
					'property' => 'background-color',
					'selector' => '&.style-3 .dot.active',
				],
			],
		];

		$this->controls['border_width'] = [
			'group'    => 'progressbar_style',
			'label'    => esc_html__('Border Width', 'the-bricksfly'),
			'type'     => 'number',
			'unit'     => 'px',
			'min'      => 0,
			'step'     => 1,
			'default'  => 1,
			'css'      => [
				[
					'property' => 'border-width',
					'selector' => '&.style-3 .dot',
				],
			],
			'required' => ['element_list', '=', '3'],
		];

		$this->controls['stroke_width'] = [
			'group'    => 'progressbar_style',
			'label'    => esc_html__('Stroke Width', 'the-bricksfly'),
			'type'     => 'number',
			'min'      => 0,
			'step'     => 0.1,
			'default'  => 2,
			'description' => esc_html__('Stroke width in em (line/circle).', 'the-bricksfly'),
			'required' => ['element_list', '!=', '3'],
		];

		$this->controls['trail_width'] = [
			'group'    => 'progressbar_style',
			'label'    => esc_html__('Trail Width', 'the-bricksfly'),
			'type'     => 'number',
			'min'      => 0,
			'step'     => 0.1,
			'default'  => 1,
			'description' => esc_html__('Trail width in em (line/circle).', 'the-bricksfly'),
			'required' => ['element_list', '!=', '3'],
		];

		$this->controls['progress_size'] = [
			'group'    => 'progressbar_style',
			'label'    => esc_html__('Size', 'the-bricksfly'),
			'type'     => 'number',
			'unit'     => 'px',
			'min'      => 1,
			'step'     => 1,
			'css'      => [
				[
					'property' => 'width',
					'selector' => '&.style-2',
				],
				[
					'property' => 'height',
					'selector' => '&.style-2',
				],
				[
					'property' => 'width',
					'selector' => '&.style-3 .dot',
				],
				[
					'property' => 'height',
					'selector' => '&.style-3 .dot',
				],
			],
			'required' => ['element_list', '!=', '1'],
		];

		/* =========================
		   PERCENTAGE STYLE
		========================= */

		$this->controls['percentage_typography'] = [
			'group'    => 'percentage_style',
			'label'    => esc_html__('Typography', 'the-bricksfly'),
			'type'     => 'typography',
			'css'      => [
				[
					'property' => 'typography',
					'selector' => '.progressbar-text',
				],
			],
			'required' => ['display_percentage', '=', true],
		];

		$this->controls['percentage_position'] = [
			'group'    => 'percentage_style',
			'label'    => esc_html__('Position Y', 'the-bricksfly'),
			'type'     => 'number',
			'unit'     => 'px',
			'step'     => 1,
			'default'  => -30,
			'css'      => [
				[
					'property' => 'top',
					'selector' => '&.style-1 .progressbar-text',
				],
			],
			'required' => [
				['display_percentage', '=', true],
				['element_list', '=', '1'],
			],
		];
	}

	public function render()
	{
		$s = $this->settings;

		$style = isset($s['element_list']) ? (string) $s['element_list'] : '1';
		if (! in_array($style, ['1', '2', '3'], true)) {
			$style = '1';
		}

		$percentage = isset($s['percentage']) ? floatval($s['percentage']) : 50;
		if ($percentage < 0) $percentage = 0;
		if ($percentage > 100) $percentage = 100;

		$progress_type = '1' === $style ? 'line' : ('2' === $style ? 'circle' : 'dot');

		$progressbar_settings = [
			'percentage'    => $percentage,
			'progress-type' => $progress_type,
		];

		if (in_array($style, ['1', '2'], true)) {
			// Read color from the Bricks color control structure: ['hex'|'rgb'|'hsl' => ...]
			$color = $this->extract_color($s['color'] ?? null, '#7DDED8');
			$bg    = $this->extract_color($s['bg_color'] ?? null, '');

			$progressbar_settings['color']        = $color;
			$progressbar_settings['trail-color']  = $bg;
			$progressbar_settings['stroke-width'] = isset($s['stroke_width']) ? floatval($s['stroke_width']) : 2;
			$progressbar_settings['trail-width']  = isset($s['trail_width']) ? floatval($s['trail_width']) : 1;
		}

		if (! empty($s['display_percentage'])) {
			$progressbar_settings['display-percentage'] = 'show';
		}

		$this->set_attribute('_root', 'class', ['aab-progressbar', 'style-' . $style]);
		$this->set_attribute('_root', 'data-aab-progressbar', wp_json_encode($progressbar_settings));

		echo '<div ' . wp_kses_post($this->render_attributes('_root')) . '>';

		if ('3' === $style) {
			echo '<div class="progressbar dots">';
			for ($i = 0; $i < 5; $i++) {
				echo '<span class="dot"></span>';
			}
			echo '</div>';
		} else {
			echo '<div class="progressbar"></div>';
		}

		echo '</div>';
	}

	/**
	 * Extract a usable color string from Bricks' color control value.
	 * Bricks stores colors as ['hex' => '#xxxxxx', 'rgb' => 'rgba(...)', 'hsl' => 'hsl(...)'].
	 */
	private function extract_color($value, $fallback = '')
	{
		if (empty($value)) {
			return $fallback;
		}
		if (is_string($value)) {
			return $value;
		}
		if (is_array($value)) {
			if (! empty($value['rgb']))  return $value['rgb'];
			if (! empty($value['hex']))  return $value['hex'];
			if (! empty($value['hsl']))  return $value['hsl'];
		}
		return $fallback;
	}
}
