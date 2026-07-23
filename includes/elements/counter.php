<?php

if (! defined('ABSPATH')) exit;

class AAB_Bricks_Counter extends \Bricks\Element
{

	public $category     = 'bricks fly';
	public $name         = 'aab-counter';
	public $icon         = 'ti-timer aab-element-marker';
	public $css_selector = '.aab-counter';
	public $scripts      = ['aabCounter'];

	public function get_label()
	{
		return esc_html__('Counter', 'the-bricksfly');
	}

	public function get_keywords()
	{
		return ['counter', 'number', 'count', 'stats', 'animation'];
	}

	public function enqueue_scripts()
	{
		wp_enqueue_style(
			'aab-counter',
			AAB_ADDONS_URL . 'public/build/elements/counter.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-counter',
			AAB_ADDONS_URL . 'public/build/elements/counter.js',
			['bricks-scripts'],
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

		$this->control_groups['animation'] = [
			'title' => esc_html__('Animation', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['layout'] = [
			'title' => esc_html__('Layout', 'the-bricksfly'),
			'tab'   => 'content',
		];

		$this->control_groups['number_style'] = [
			'title' => esc_html__('Number', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['prefix_suffix_style'] = [
			'title' => esc_html__('Prefix / Suffix', 'the-bricksfly'),
			'tab'   => 'style',
		];

		$this->control_groups['title_style'] = [
			'title' => esc_html__('Title', 'the-bricksfly'),
			'tab'   => 'style',
		];
	}

	public function set_controls()
	{

		/* =========================
		   CONTENT
		========================= */

		$this->controls['starting_number'] = [
			'group'   => 'content',
			'label'   => esc_html__('Starting Number', 'the-bricksfly'),
			'type'    => 'number',
			'default' => 0,
		];

		$this->controls['ending_number'] = [
			'group'   => 'content',
			'label'   => esc_html__('Ending Number', 'the-bricksfly'),
			'type'    => 'number',
			'default' => 100,
		];

		$this->controls['number_prefix'] = [
			'group'       => 'content',
			'label'       => esc_html__('Number Prefix', 'the-bricksfly'),
			'type'        => 'text',
			'placeholder' => '$',
		];

		$this->controls['number_suffix'] = [
			'group'       => 'content',
			'label'       => esc_html__('Number Suffix', 'the-bricksfly'),
			'type'        => 'text',
			'placeholder' => '+',
		];

		$this->controls['counter_title'] = [
			'group'   => 'content',
			'label'   => esc_html__('Title', 'the-bricksfly'),
			'type'    => 'text',
			'default' => esc_html__('Happy Clients', 'the-bricksfly'),
		];

		$this->controls['title_tag'] = [
			'group'   => 'content',
			'label'   => esc_html__('Title HTML Tag', 'the-bricksfly'),
			'type'    => 'select',
			'options' => [
				'h1' => 'H1',
				'h2' => 'H2',
				'h3' => 'H3',
				'h4' => 'H4',
				'h5' => 'H5',
				'h6' => 'H6',
				'p'  => 'P',
				'span' => 'SPAN',
				'div' => 'DIV',
			],
			'default' => 'p',
			'inline'  => true,
		];

		$this->controls['thousand_separator'] = [
			'group'   => 'content',
			'label'   => esc_html__('Thousand Separator', 'the-bricksfly'),
			'type'    => 'select',
			'options' => [
				'none'  => esc_html__('None', 'the-bricksfly'),
				'comma' => esc_html__('Comma (1,000)', 'the-bricksfly'),
				'dot'   => esc_html__('Dot (1.000)', 'the-bricksfly'),
				'space' => esc_html__('Space (1 000)', 'the-bricksfly'),
			],
			'default' => 'comma',
		];

		/* =========================
		   ANIMATION
		========================= */

		$this->controls['animation_duration'] = [
			'group'   => 'animation',
			'label'   => esc_html__('Duration (ms)', 'the-bricksfly'),
			'type'    => 'number',
			'default' => 2000,
			'step'    => 100,
			'min'     => 200,
		];

		$this->controls['animation_trigger'] = [
			'group'   => 'animation',
			'label'   => esc_html__('Trigger', 'the-bricksfly'),
			'type'    => 'select',
			'options' => [
				'on_scroll'    => esc_html__('On Scroll (In View)', 'the-bricksfly'),
				'on_page_load' => esc_html__('On Page Load', 'the-bricksfly'),
			],
			'default' => 'on_scroll',
		];

		/* =========================
		   LAYOUT
		========================= */

		$this->controls['direction'] = [
			'group'   => 'layout',
			'label'   => esc_html__('Direction', 'the-bricksfly'),
			'type'    => 'select',
			'options' => [
				'column'         => esc_html__('Vertical (Number Top)', 'the-bricksfly'),
				'column-reverse' => esc_html__('Vertical (Title Top)', 'the-bricksfly'),
				'row'            => esc_html__('Horizontal (Number Left)', 'the-bricksfly'),
				'row-reverse'    => esc_html__('Horizontal (Title Left)', 'the-bricksfly'),
			],
			'default' => 'column',
			'css'     => [[
				'property' => 'flex-direction',
				'selector' => '.aab-counter__inner',
			]],
		];

		$this->controls['alignment'] = [
			'group'   => 'layout',
			'label'   => esc_html__('Alignment', 'the-bricksfly'),
			'type'    => 'justify-content',
			'css'     => [
				[
					'property' => 'align-items',
					'selector' => '.aab-counter__inner',
				],
			],
			'exclude' => ['space-between', 'space-around', 'space-evenly'], // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Bricks control option, not a WP_Query arg
		];

		$this->controls['gap'] = [
			'group' => 'layout',
			'label' => esc_html__('Gap', 'the-bricksfly'),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'gap',
				'selector' => '.aab-counter__inner',
			]],
		];

		$this->controls['number_gap'] = [
			'group' => 'layout',
			'label' => esc_html__('Number Row Gap', 'the-bricksfly'),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => 'gap',
				'selector' => '.aab-counter__number-wrap',
			]],
		];

		/* =========================
		   NUMBER STYLE
		========================= */

		$this->controls['number_typo'] = [
			'group' => 'number_style',
			'label' => esc_html__('Typography', 'the-bricksfly'),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.aab-counter__number',
			]],
		];

		$this->controls['number_bg'] = [
			'group' => 'number_style',
			'label' => esc_html__('Background', 'the-bricksfly'),
			'type'  => 'color',
			'css'   => [[
				'property' => 'background-color',
				'selector' => '.aab-counter__number',
			]],
		];

		$this->controls['number_padding'] = [
			'group' => 'number_style',
			'label' => esc_html__('Padding', 'the-bricksfly'),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'padding',
				'selector' => '.aab-counter__number',
			]],
		];

		$this->controls['number_border'] = [
			'group' => 'number_style',
			'label' => esc_html__('Border', 'the-bricksfly'),
			'type'  => 'border',
			'css'   => [[
				'property' => 'border',
				'selector' => '.aab-counter__number',
			]],
		];

		// $this->controls['number_radius'] = [
		// 	'group' => 'number_style',
		// 	'label' => esc_html__( 'Border Radius', 'the-bricksfly' ),
		// 	'type'  => 'dimensions',
		// 	'css'   => [[
		// 		'property' => 'border-radius',
		// 		'selector' => '.aab-counter__number',
		// 	]],
		// ];

		/* =========================
		   PREFIX / SUFFIX STYLE
		========================= */

		$this->controls['prefix_typo'] = [
			'group' => 'prefix_suffix_style',
			'label' => esc_html__('Prefix Typography', 'the-bricksfly'),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.aab-counter__prefix',
			]],
		];

		$this->controls['suffix_typo'] = [
			'group' => 'prefix_suffix_style',
			'label' => esc_html__('Suffix Typography', 'the-bricksfly'),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.aab-counter__suffix',
			]],
		];

		/* =========================
		   TITLE STYLE
		========================= */

		$this->controls['title_typo'] = [
			'group' => 'title_style',
			'label' => esc_html__('Typography', 'the-bricksfly'),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.aab-counter__title',
			]],
		];

		$this->controls['title_margin'] = [
			'group' => 'title_style',
			'label' => esc_html__('Margin', 'the-bricksfly'),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'margin',
				'selector' => '.aab-counter__title',
			]],
		];
	}

	public function render()
	{
		$s = $this->settings;

		$start     = isset($s['starting_number']) ? floatval($s['starting_number']) : 0;
		$end       = isset($s['ending_number']) ? floatval($s['ending_number']) : 100;
		$prefix    = $s['number_prefix'] ?? '';
		$suffix    = $s['number_suffix'] ?? '';
		$title     = $s['counter_title'] ?? '';
		$tag       = $s['title_tag'] ?? 'p';
		$separator = $s['thousand_separator'] ?? 'comma';
		$duration  = isset($s['animation_duration']) ? intval($s['animation_duration']) : 2000;
		$trigger   = $s['animation_trigger'] ?? 'on_scroll';

		$allowed_tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span', 'div'];
		$tag = in_array($tag, $allowed_tags, true) ? $tag : 'p';

		// Check if number has decimals
		$decimals = 0;
		if (floor($end) != $end) {
			$decimals = strlen(substr(strrchr((string) $end, '.'), 1));
		}
		if (floor($start) != $start) {
			$start_dec = strlen(substr(strrchr((string) $start, '.'), 1));
			$decimals = max($decimals, $start_dec);
		}

		$counter_data = [
			'start'     => $start,
			'end'       => $end,
			'duration'  => $duration,
			'separator' => $separator,
			'decimals'  => $decimals,
			'trigger'   => $trigger,
		];

		$this->set_attribute('_root', 'data-aab-counter', wp_json_encode($counter_data));

		echo wp_kses_post('<div ' . $this->render_attributes('_root') . '>');
		echo '<div class="aab-counter__inner">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Number row: prefix + number + suffix
		echo '<div class="aab-counter__number-wrap">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if (! empty($prefix)) {
			echo '<span class="aab-counter__prefix">' . esc_html($prefix) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Display the starting number formatted
		$display = $this->format_number($start, $decimals, $separator);
		echo '<span class="aab-counter__number">' . esc_html($display) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if (! empty($suffix)) {
			echo '<span class="aab-counter__suffix">' . esc_html($suffix) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Title
		if (! empty($title)) {
			echo '<' . tag_escape($tag) . ' class="aab-counter__title">' . esc_html($title) . '</' . tag_escape($tag) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function format_number($number, $decimals, $separator)
	{
		$sep_char = '';
		switch ($separator) {
			case 'comma':
				$sep_char = ',';
				break;
			case 'dot':
				$sep_char = '.';
				break;
			case 'space':
				$sep_char = ' ';
				break;
		}

		$dec_point = ($separator === 'dot') ? ',' : '.';

		return number_format($number, $decimals, $dec_point, $sep_char);
	}
}
