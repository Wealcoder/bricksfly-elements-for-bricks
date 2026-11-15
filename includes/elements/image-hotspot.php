<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BRICKSFLY_Bricks_Image_Hotspot extends \Bricks\Element {


	public $category     = 'bricks fly';
	public $name         = 'aab-image-hotspot';
	public $icon         = 'ti-location-pin aab-element-marker';
	public $css_selector = '.aab-image-hotspot';
	public $scripts      = array( 'bricksflyImageHotspot' );

	public function get_label() {
		return esc_html__( 'Image Hotspot', 'bricksfly-elements-for-bricks' );
	}

	public function get_keywords() {
		return array( 'image', 'hotspot', 'tooltip', 'marker', 'pin', 'annotation' );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'bricks-font-awesome-6' );

		wp_enqueue_style(
			'aab-image-hotspot',
			BRICKSFLY_URL . 'public/build/elements/image-hotspot.css',
			array(),
			'1.0.0'
		);

		wp_enqueue_script(
			'aab-image-hotspot',
			BRICKSFLY_URL . 'public/build/elements/image-hotspot.js',
			array(),
			'1.0.0',
			true
		);
	}

	public function set_control_groups() {
		$this->control_groups['image'] = array(
			'title' => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['hotspots'] = array(
			'title' => esc_html__( 'Hotspot', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['tooltip'] = array(
			'title' => esc_html__( 'Tooltip', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'content',
		);

		$this->control_groups['image_style'] = array(
			'title' => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['hotspot_style'] = array(
			'title' => esc_html__( 'Hotspot', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);

		$this->control_groups['tooltip_style'] = array(
			'title' => esc_html__( 'Tooltip', 'bricksfly-elements-for-bricks' ),
			'tab'   => 'style',
		);
	}

	public function set_controls() {
		$icon    = '.aab-hotspot__icon';
		$tooltip = '.aab-hotspot__tooltip';

		/*
		=========================
			IMAGE
		========================= */

		$this->controls['image'] = array(
			'group'   => 'image',
			'label'   => esc_html__( 'Image', 'bricksfly-elements-for-bricks' ),
			'type'    => 'image',
			'default' => array(
				'url' => \Bricks\Builder::get_template_placeholder_image(),
			),
		);

		/*
		=========================
			HOTSPOTS
			Each repeater item is addressed by Bricks as
			`.repeater-item:nth-child(n)`, so the items live alone inside
			.aab-image-hotspot__items (the <img> must not be their sibling).
		========================= */

		$this->controls['hotspots'] = array(
			'group'         => 'hotspots',
			'label'         => esc_html__( 'Hotspot Lists', 'bricksfly-elements-for-bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'text',
			'fields'        => array(
				'layout'          => array(
					'label'   => esc_html__( 'Hotspot Layout', 'bricksfly-elements-for-bricks' ),
					'type'    => 'select',
					'options' => array(
						'dot'       => esc_html__( 'Default', 'bricksfly-elements-for-bricks' ),
						'icon'      => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
						'text'      => esc_html__( 'Text', 'bricksfly-elements-for-bricks' ),
						'icon-text' => esc_html__( 'Icon + Text', 'bricksfly-elements-for-bricks' ),
					),
					'default' => 'dot',
				),
				'tooltip_type'    => array(
					'label'   => esc_html__( 'Tooltip Type', 'bricksfly-elements-for-bricks' ),
					'type'    => 'select',
					'options' => array(
						'tooltip' => esc_html__( 'Content', 'bricksfly-elements-for-bricks' ),
						'link'    => esc_html__( 'Link', 'bricksfly-elements-for-bricks' ),
					),
					'default' => 'tooltip',
				),
				'icon'            => array(
					'label'    => esc_html__( 'Icon', 'bricksfly-elements-for-bricks' ),
					'type'     => 'icon',
					'default'  => array(
						'library' => 'fontawesome',
						'icon'    => 'fas fa-circle',
					),
					'required' => array( 'layout', '=', array( 'icon', 'icon-text' ) ),
				),
				'text'            => array(
					'label'       => esc_html__( 'Text', 'bricksfly-elements-for-bricks' ),
					'type'        => 'text',
					'placeholder' => esc_html__( 'Type your text here', 'bricksfly-elements-for-bricks' ),
					'required'    => array( 'layout', '=', array( 'text', 'icon-text' ) ),
				),
				'tooltip_content' => array(
					'label'    => esc_html__( 'Tooltip Content', 'bricksfly-elements-for-bricks' ),
					'type'     => 'editor',
					'default'  => esc_html__( 'Tooltip content', 'bricksfly-elements-for-bricks' ),
					'required' => array( 'tooltip_type', '=', 'tooltip' ),
				),
				'link'            => array(
					'label'    => esc_html__( 'Link', 'bricksfly-elements-for-bricks' ),
					'type'     => 'link',
					'required' => array( 'tooltip_type', '=', 'link' ),
				),
				// No 'selector' => CSS lands on the repeater item itself.
				'pos_left'        => array(
					'label'       => esc_html__( 'Horizontal Offset', 'bricksfly-elements-for-bricks' ),
					'type'        => 'number',
					'units'       => true,
					'placeholder' => '50%',
					'css'         => array(
						array(
							'property' => 'left',
						),
					),
				),
				'pos_top'         => array(
					'label'       => esc_html__( 'Vertical Offset', 'bricksfly-elements-for-bricks' ),
					'type'        => 'number',
					'units'       => true,
					'placeholder' => '50%',
					'css'         => array(
						array(
							'property' => 'top',
						),
					),
				),
			),
			'default'       => array(
				array(
					'layout'          => 'dot',
					'tooltip_type'    => 'tooltip',
					'tooltip_content' => esc_html__( 'Tooltip content', 'bricksfly-elements-for-bricks' ),
					'pos_left'        => '30%',
					'pos_top'         => '40%',
				),
				array(
					'layout'          => 'icon',
					'icon'            => array(
						'library' => 'fontawesome',
						'icon'    => 'fas fa-plus',
					),
					'tooltip_type'    => 'tooltip',
					'tooltip_content' => esc_html__( 'Tooltip content', 'bricksfly-elements-for-bricks' ),
					'pos_left'        => '65%',
					'pos_top'         => '60%',
				),
			),
		);

		$this->controls['animation'] = array(
			'group'   => 'hotspots',
			'label'   => esc_html__( 'Animation', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'beat'  => esc_html__( 'Soft Beat', 'bricksfly-elements-for-bricks' ),
				'pulse' => esc_html__( 'Pulse', 'bricksfly-elements-for-bricks' ),
				'none'  => esc_html__( 'None', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'pulse',
		);

		$this->controls['animation_speed'] = array(
			'group'    => 'hotspots',
			'label'    => esc_html__( 'Animation Speed (s)', 'bricksfly-elements-for-bricks' ),
			'type'     => 'number',
			'min'      => 0.1,
			'max'      => 10,
			'step'     => 0.1,
			'default'  => 3,
			'required' => array( 'animation', '=', array( 'beat', 'pulse' ) ),
			'css'      => array(
				array(
					'property' => '--anim-speed',
					'selector' => $icon,
					'value'    => '%ss',
				),
			),
		);

		$this->controls['pulse_color'] = array(
			'group'    => 'hotspots',
			'label'    => esc_html__( 'Pulse Color', 'bricksfly-elements-for-bricks' ),
			'type'     => 'color',
			'required' => array( 'animation', '=', 'pulse' ),
			'css'      => array(
				array(
					'property' => '--pulse-color',
					'selector' => $icon,
				),
			),
		);

		/*
		=========================
			TOOLTIP
		========================= */

		$this->controls['trigger_type'] = array(
			'group'   => 'tooltip',
			'label'   => esc_html__( 'Trigger Type', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'hover' => esc_html__( 'Hover', 'bricksfly-elements-for-bricks' ),
				'click' => esc_html__( 'Click', 'bricksfly-elements-for-bricks' ),
				'none'  => esc_html__( 'None (always visible)', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'hover',
		);

		$this->controls['tooltip_position'] = array(
			'group'   => 'tooltip',
			'label'   => esc_html__( 'Position', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'top'    => esc_html__( 'Top', 'bricksfly-elements-for-bricks' ),
				'bottom' => esc_html__( 'Bottom', 'bricksfly-elements-for-bricks' ),
				'left'   => esc_html__( 'Left', 'bricksfly-elements-for-bricks' ),
				'right'  => esc_html__( 'Right', 'bricksfly-elements-for-bricks' ),
			),
			'default' => 'bottom',
		);

		$this->controls['tooltip_gap'] = array(
			'group' => 'tooltip',
			'label' => esc_html__( 'Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => '--tlp-gap',
					'selector' => $tooltip,
				),
			),
		);

		$this->controls['tooltip_align'] = array(
			'group'   => 'tooltip',
			'label'   => esc_html__( 'Alignment', 'bricksfly-elements-for-bricks' ),
			'type'    => 'select',
			'options' => array(
				'left'   => esc_html__( 'Left', 'bricksfly-elements-for-bricks' ),
				'center' => esc_html__( 'Center', 'bricksfly-elements-for-bricks' ),
				'right'  => esc_html__( 'Right', 'bricksfly-elements-for-bricks' ),
			),
			'inline'  => true,
			'css'     => array(
				array(
					'property' => 'text-align',
					'selector' => $tooltip,
				),
			),
		);

		/*
		=========================
			IMAGE STYLE
		========================= */

		$this->controls['img_width'] = array(
			'group' => 'image_style',
			'label' => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => '.aab-image-hotspot',
				),
			),
		);

		$this->controls['img_height'] = array(
			'group' => 'image_style',
			'label' => esc_html__( 'Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => '.aab-image-hotspot',
				),
			),
		);

		$this->controls['img_radius'] = array(
			'group' => 'image_style',
			'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'border-radius',
					'selector' => '.aab-image-hotspot img',
				),
			),
		);

		/*
		=========================
			HOTSPOT STYLE
		========================= */

		$this->controls['hsp_bg'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'background',
			'css'   => array(
				array(
					'property' => 'background',
					'selector' => $icon,
				),
			),
		);

		// The pulse rings are pseudo-elements of the icon, so the radius
		// must follow the border or the rings stay round around a square dot.
		$this->controls['hsp_border'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Border', 'bricksfly-elements-for-bricks' ),
			'type'  => 'border',
			'css'   => array(
				array(
					'property' => 'border',
					'selector' => $icon,
				),
				array(
					'property' => 'border-radius',
					'selector' => '.aab-image-hotspot--pulse ' . $icon . '::before',
				),
				array(
					'property' => 'border-radius',
					'selector' => '.aab-image-hotspot--pulse ' . $icon . '::after',
				),
			),
		);

		$this->controls['hsp_padding'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => $icon,
				),
			),
		);

		$this->controls['dot_width'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Dot Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => $icon . '--dot',
				),
			),
		);

		$this->controls['dot_height'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Dot Height', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'height',
					'selector' => $icon . '--dot',
				),
			),
		);

		$this->controls['text_color'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Text Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => $icon . '--text, ' . $icon . '--icon-text',
				),
				array(
					'property' => 'fill',
					'selector' => $icon . '--text, ' . $icon . '--icon-text',
				),
			),
		);

		$this->controls['text_typo'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Text Typography', 'bricksfly-elements-for-bricks' ),
			'type'  => 'typography',
			'css'   => array(
				array(
					'property' => 'typography',
					'selector' => $icon . '--text, ' . $icon . '--icon-text',
				),
			),
		);

		$this->controls['icon_color'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Icon Color', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'color',
					'selector' => $icon . '--icon',
				),
				array(
					'property' => 'fill',
					'selector' => $icon . '--icon',
				),
			),
		);

		$this->controls['icon_size'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Icon Size', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'font-size',
					'selector' => $icon . ' svg, ' . $icon . ' i',
				),
			),
		);

		$this->controls['icon_gap'] = array(
			'group' => 'hotspot_style',
			'label' => esc_html__( 'Icon Gap', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'gap',
					'selector' => $icon,
				),
			),
		);

		/*
		=========================
			TOOLTIP STYLE
		========================= */

		// Colour (not a full background) so the arrow, a ::after shape, can share it.
		$this->controls['tlp_bg'] = array(
			'group' => 'tooltip_style',
			'label' => esc_html__( 'Background', 'bricksfly-elements-for-bricks' ),
			'type'  => 'color',
			'css'   => array(
				array(
					'property' => 'background-color',
					'selector' => $tooltip,
				),
				array(
					'property' => 'background-color',
					'selector' => $tooltip . '::after',
				),
			),
		);

		$this->controls['tlp_width'] = array(
			'group' => 'tooltip_style',
			'label' => esc_html__( 'Width', 'bricksfly-elements-for-bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => array(
				array(
					'property' => 'width',
					'selector' => $tooltip,
				),
			),
		);

		$this->controls['tlp_radius'] = array(
			'group' => 'tooltip_style',
			'label' => esc_html__( 'Border Radius', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'border-radius',
					'selector' => $tooltip,
				),
			),
		);

		$this->controls['tlp_padding'] = array(
			'group' => 'tooltip_style',
			'label' => esc_html__( 'Padding', 'bricksfly-elements-for-bricks' ),
			'type'  => 'dimensions',
			'css'   => array(
				array(
					'property' => 'padding',
					'selector' => $tooltip,
				),
			),
		);
	}

	public function render() {
		$s     = $this->settings;
		$items = ! empty( $s['hotspots'] ) && is_array( $s['hotspots'] ) ? $s['hotspots'] : array();

		if ( empty( $items ) ) {
			return $this->render_element_placeholder(
				array(
					'title' => esc_html__( 'No hotspot added.', 'bricksfly-elements-for-bricks' ),
				)
			);
		}

		$animation = $s['animation'] ?? 'pulse';
		$animation = in_array( $animation, array( 'beat', 'pulse', 'none' ), true ) ? $animation : 'pulse';

		$position = $s['tooltip_position'] ?? 'bottom';
		$position = in_array( $position, array( 'top', 'bottom', 'left', 'right' ), true ) ? $position : 'bottom';

		$trigger = $s['trigger_type'] ?? 'hover';
		$trigger = in_array( $trigger, array( 'hover', 'click', 'none' ), true ) ? $trigger : 'hover';

		$this->set_attribute(
			'box',
			'class',
			array(
				'aab-image-hotspot',
				'aab-image-hotspot--' . $animation,
				'aab-image-hotspot--tlp-' . $position,
			)
		);

		echo '<div ' . $this->render_attributes( '_root' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bricks escapes attributes.
		echo '<div ' . $this->render_attributes( 'box' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$this->render_image( $s );

		echo '<div class="aab-image-hotspot__items">';

		foreach ( $items as $index => $item ) {
			$this->render_item( $index, $item, $trigger );
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	private function render_image( $s ) {
		$image = $this->resolve_image( $s['image'] ?? array() );

		if ( ! empty( $image['id'] ) ) {
			echo wp_get_attachment_image( (int) $image['id'], $image['size'], false, array( 'class' => 'aab-image-hotspot__img' ) );
		} elseif ( ! empty( $image['url'] ) ) {
			echo '<img class="aab-image-hotspot__img" src="' . esc_url( $image['url'] ) . '" alt="" />';
		}
	}

	/**
	 * Normalise the image control (incl. dynamic data), as Bricks' Image element does.
	 */
	private function resolve_image( $image ) {
		if ( empty( $image ) ) {
			return array();
		}

		$image['size'] = empty( $image['size'] ) ? BRICKS_DEFAULT_IMAGE_SIZE : $image['size'];

		if ( ! empty( $image['useDynamicData'] ) ) {
			$images = $this->render_dynamic_data_tag( $image['useDynamicData'], 'image', array( 'size' => $image['size'] ) );

			if ( empty( $images[0] ) ) {
				return array();
			}

			if ( is_numeric( $images[0] ) ) {
				$image['id'] = (int) $images[0];
				unset( $image['url'] );
			} else {
				$image['id']  = 0;
				$image['url'] = $images[0];
			}
		}

		if ( ! empty( $image['id'] ) && empty( $image['url'] ) ) {
			$image['url'] = wp_get_attachment_image_url( $image['id'], $image['size'] );
		}

		return $image;
	}

	private function render_item( $index, $item, $trigger ) {
		$layout = $item['layout'] ?? 'dot';
		$layout = in_array( $layout, array( 'dot', 'icon', 'text', 'icon-text' ), true ) ? $layout : 'dot';
		$type   = $item['tooltip_type'] ?? 'tooltip';

		$this->set_attribute(
			"item-{$index}",
			'class',
			array(
				'aab-hotspot',
				'repeater-item',
				'aab-hotspot--' . $trigger,
			)
		);

		$is_link = 'link' === $type;
		$tag     = 'div';

		if ( $is_link ) {
			$tag = 'a';
			if ( ! empty( $item['link']['url'] ) && method_exists( $this, 'set_link_attributes' ) ) {
				$this->set_link_attributes( "item-{$index}", $item['link'] );
			}
		}

		echo '<' . tag_escape( $tag ) . ' ' . $this->render_attributes( "item-{$index}" ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="aab-hotspot__icon aab-hotspot__icon--' . esc_attr( $layout ) . '">';

		$text = isset( $item['text'] ) ? esc_html( $item['text'] ) : '';
		$icon = ! empty( $item['icon'] ) ? self::render_icon( $item['icon'], array( 'aria-hidden' => 'true' ) ) : '';

		if ( 'text' === $layout ) {
			echo $text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		} elseif ( 'icon' === $layout ) {
			echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Bricks-rendered icon markup.
		} elseif ( 'icon-text' === $layout ) {
			echo $text . $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>';

		if ( ! $is_link ) {
			echo '<div class="aab-hotspot__tooltip">' . wp_kses_post( $item['tooltip_content'] ?? '' ) . '</div>';
		}

		echo '</' . tag_escape( $tag ) . '>';
	}
}
