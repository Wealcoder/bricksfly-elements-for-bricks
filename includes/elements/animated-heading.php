<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class BRICKSFLY_Bricks_Animated_Heading extends \Bricks\Element {

	public $category     = 'bricks fly';
	public $name         = 'aab-animated-heading';
	public $icon         = 'ti-text aab-element-marker';
	public $css_selector = '.bricksfly-animated-heading';
	public $scripts      = [ 'bricksflyAnimatedHeading' ];

	public function get_label() {
		return esc_html__('Animated Heading', 'bricksfly-elements-for-bricks');
	}

	public function get_keywords() {
		return [ 'animated', 'heading', 'text', 'title', 'animation' ];
	}

	public function enqueue_scripts() {
		wp_enqueue_style(
			'aab-animated-heading',
			BRICKSFLY_URL . 'public/build/elements/animated-heading.css',
			[],
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-animated-heading',
			BRICKSFLY_URL . 'public/build/elements/animated-heading.js',
			[],
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['content'] = [
			'title' => esc_html__('Content', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['animation'] = [
			'title' => esc_html__('Animation', 'bricksfly-elements-for-bricks'),
			'tab'   => 'content',
		];

		$this->control_groups['heading_style'] = [
			'title' => esc_html__('Heading', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];

		$this->control_groups['text_effects'] = [
			'title' => esc_html__('Text Effects', 'bricksfly-elements-for-bricks'),
			'tab'   => 'style',
		];
	}

	public function set_controls() {

		/* =========================
		   CONTENT
		========================= */

		$this->controls['heading_text'] = [
			'group'   => 'content',
			'label'   => esc_html__( 'Heading Text', 'bricksfly-elements-for-bricks' ),
			'type'    => 'text',
			'default' => esc_html__( 'Animated Heading', 'bricksfly-elements-for-bricks' ),
		];

		$this->controls['heading_tag'] = [
			'group'   => 'content',
			'label'   => esc_html__( 'HTML Tag', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => [
				'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3',
				'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
			],
			'default' => 'h2',
			'inline'  => true,
		];

		$this->controls['heading_link'] = [
			'group' => 'content',
			'label' => esc_html__( 'Link', 'bricksfly-elements-for-bricks' ),
			'type'  => 'link',
		];

		$this->controls['text_align'] = [
			'group' => 'content',
			'label' => esc_html__( 'Text Align', 'bricksfly-elements-for-bricks' ),
			'type'  => 'text-align',
			'css'   => [[
				'property' => 'text-align',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		/* =========================
		   ANIMATION
		========================= */

		$this->controls['animation_type'] = [
			'group'   => 'animation',
			'label'   => esc_html__( 'Animation Type', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => [
				'none'             => esc_html__( 'None', 'bricksfly-elements-for-bricks' ),
				'reveal'           => esc_html__( 'Reveal', 'bricksfly-elements-for-bricks' ),
				'scale'            => esc_html__( 'Scale', 'bricksfly-elements-for-bricks' ),
				'slide'            => esc_html__( 'Slide', 'bricksfly-elements-for-bricks' ),
				'skew_reveal'      => esc_html__( 'Skew Reveal', 'bricksfly-elements-for-bricks' ),
				'glow_pulse'       => esc_html__( 'Glow Pulse', 'bricksfly-elements-for-bricks' ),
				'typewriter'       => esc_html__( 'Typewriter', 'bricksfly-elements-for-bricks' ),
				'mask_wipe'        => esc_html__( 'Mask Wipe', 'bricksfly-elements-for-bricks' ),
				'water_wave'       => esc_html__( 'Water Wave', 'bricksfly-elements-for-bricks' ),
				'background_clip'  => esc_html__( 'Background Clip Text', 'bricksfly-elements-for-bricks' ),
				'character'        => esc_html__( 'Character Animation', 'bricksfly-elements-for-bricks' ),
			],
			'default' => 'reveal',
		];

		$this->controls['animation_trigger'] = [
			'group'   => 'animation',
			'label'   => esc_html__( 'Trigger', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => [
				'on_scroll'        => esc_html__( 'On Scroll', 'bricksfly-elements-for-bricks' ),
				'on_page_load'     => esc_html__( 'On Page Load', 'bricksfly-elements-for-bricks' ),
				'play_with_scroll' => esc_html__( 'Play With Scroll', 'bricksfly-elements-for-bricks' ),
				'mouseover'        => esc_html__( 'Hover', 'bricksfly-elements-for-bricks' ),
				'click'            => esc_html__( 'Click', 'bricksfly-elements-for-bricks' ),
			],
			'default'  => 'on_scroll',
			'required' => [['animation_type', '!=', 'none']],
		];

		$this->controls['trigger_selector'] = [
			'group'       => 'animation',
			'label'       => esc_html__( 'Trigger Selector', 'bricksfly-elements-for-bricks' ),
			'type'        => 'text',
			'placeholder' => '.my-class',
			'required'    => [
				['animation_trigger', '=', ['mouseover', 'click']],
				['animation_type', '!=', 'none'],
			],
		];

		$this->controls['animation_duration'] = [
			'group'    => 'animation',
			'label'    => esc_html__( 'Duration (s)', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => 1,
			'step'     => 0.1,
			'required' => [['animation_type', '!=', 'none']],
		];

		$this->controls['animation_delay'] = [
			'group'    => 'animation',
			'label'    => esc_html__( 'Delay (s)', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => 0,
			'step'     => 0.1,
			'required' => [['animation_type', '!=', 'none']],
		];

		$this->controls['animation_stagger'] = [
			'group'    => 'animation',
			'label'    => esc_html__( 'Stagger (s)', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'default'  => 0.02,
			'step'     => 0.01,
			'required' => [['animation_type', '=', ['character', 'typewriter', 'water_wave']]],
		];

		/* =========================
		   HEADING STYLE
		========================= */

		$this->controls['heading_typo'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => [[
				'property' => 'typography',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		$this->controls['heading_color'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [[
				'property' => 'color',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		$this->controls['heading_margin'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Margin', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'margin',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		$this->controls['heading_padding'] = [
			'group' => 'heading_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => [[
				'property' => 'padding',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		/* =========================
		   TEXT EFFECTS
		========================= */

		$this->controls['text_shadow'] = [
			'group' => 'text_effects',
			'label' => esc_html__( 'Text Shadow', 'bricksfly-elements-for-bricks' ),
			'type'  => 'box-shadow',
			'css'   => [[
				'property' => 'text-shadow',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		$this->controls['bg_clip_gradient'] = [
			'group'    => 'text_effects',
			'label'    => esc_html__( 'Gradient Background', 'bricksfly-elements-for-bricks' ),
			'type'     => 'background',
			'required' => [['animation_type', '=', 'background_clip']],
			'css'      => [[
				'property' => 'background',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		$this->controls['stroke_color'] = [
			'group' => 'text_effects',
			'label' => esc_html__( 'Text Stroke Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => [[
				'property' => '-webkit-text-stroke-color',
				'selector' => '.bricksfly-animated-heading',
			]],
		];

		$this->controls['stroke_width'] = [
			'group' => 'text_effects',
			'label' => esc_html__( 'Text Stroke Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [[
				'property' => '-webkit-text-stroke-width',
				'selector' => '.bricksfly-animated-heading',
			]],
		];
	}

	public function render() {
		$s = $this->settings;

		$text    = $s['heading_text'] ?? esc_html__( 'Animated Heading', 'bricksfly-elements-for-bricks' );
		$tag     = $s['heading_tag'] ?? 'h2';
		$tag     = in_array( $tag, ['h1','h2','h3','h4','h5','h6'], true ) ? $tag : 'h2';

		$anim_type    = $s['animation_type'] ?? 'reveal';
		$anim_trigger = $s['animation_trigger'] ?? 'on_scroll';
		$duration     = $s['animation_duration'] ?? 1;
		$delay        = $s['animation_delay'] ?? 0;
		$stagger      = $s['animation_stagger'] ?? 0.02;
		$trigger_sel  = $s['trigger_selector'] ?? '';

		// Build animation data as JSON
		$anim_data = [
			'type'     => $anim_type,
			'trigger'  => $anim_trigger,
			'duration' => floatval( $duration ),
			'delay'    => floatval( $delay ),
			'stagger'  => floatval( $stagger ),
		];

		if ( ! empty( $trigger_sel ) ) {
			$anim_data['triggerSelector'] = $trigger_sel;
		}

		// Set attributes on the heading element
		$this->set_attribute( 'heading', 'class', 'bricksfly-animated-heading' );
		$this->set_attribute( 'heading', 'data-bricksfly-anim', esc_attr( wp_json_encode( $anim_data ) ) );

		// Link handling
		$content = esc_html( $text );

		if ( ! empty( $s['heading_link']['url'] ) ) {
			$link_open  = '<a href="' . esc_url( $s['heading_link']['url'] ) . '"';
			if ( ! empty( $s['heading_link']['newTab'] ) ) {
				$link_open .= ' target="_blank" rel="noopener noreferrer"';
			}
			$link_open .= '>';
			$content = $link_open . $content . '</a>';
		}

		echo wp_kses_post('<div ' . $this->render_attributes( '_root' ) . '>');
		echo wp_kses_post('<' . tag_escape( $tag ) . ' ' . $this->render_attributes( 'heading' ) . '>');
		echo wp_kses_post( $content );
		echo '</' . tag_escape( $tag ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
